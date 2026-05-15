import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'transfer_screen.dart';
import 'add_transaction_screen.dart';

class FundDetailScreen extends StatefulWidget {
  final int fundId;
  FundDetailScreen({required this.fundId});

  @override
  _FundDetailScreenState createState() => _FundDetailScreenState();
}

class _FundDetailScreenState extends State<FundDetailScreen> {
  final apiService = ApiService();
  Map<String, dynamic>? data;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadDetail();
  }

  void loadDetail() async {
    final result = await apiService.getFundDetail(widget.fundId);
    setState(() {
      data = result;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(data?['fund']?['name'] ?? 'تفاصيل الصندوق', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(icon: Icon(Icons.arrow_back_ios, color: Colors.indigo), onPressed: () => Navigator.pop(context)),
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : RefreshIndicator(
            onRefresh: () async => loadDetail(),
            child: SingleChildScrollView(
              padding: EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  _buildHeaderCard(),
                  SizedBox(height: 25),
                  _buildQuickActions(),
                  SizedBox(height: 30),
                  _buildSectionTitle('حسابات الصندوق'),
                  SizedBox(height: 15),
                  _buildAccountsList(),
                  SizedBox(height: 30),
                  _buildSectionTitle('توزيع الحصص'),
                  SizedBox(height: 15),
                  _buildEquitiesList(),
                  SizedBox(height: 30),
                  _buildSectionTitle('العمليات الأخيرة'),
                  SizedBox(height: 15),
                  _buildTransactionsList(),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildQuickActions() {
    return Row(
      children: [
        Expanded(child: _actionButton('إضافة عملية', Icons.add_box, Colors.indigo, () async {
          final res = await Get.to(() => AddTransactionScreen());
          if (res == true) loadDetail();
        })),
        SizedBox(width: 15),
        Expanded(child: _actionButton('تحويل داخلي', Icons.swap_horiz, Colors.amber.shade700, () async {
          final res = await Get.to(() => TransferScreen(
            fundId: widget.fundId, 
            paymentMethods: data?['payment_methods'] ?? []
          ));
          if (res == true) loadDetail();
        })),
      ],
    );
  }

  Widget _actionButton(String label, IconData icon, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 20),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(25),
          border: Border.all(color: color.withOpacity(0.2))
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 28),
            SizedBox(height: 8),
            Text(label, style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountsList() {
    final accounts = data?['payment_methods'] as List? ?? [];
    if (accounts.isEmpty) return Text('لا توجد حسابات مضافة', style: TextStyle(color: Colors.grey, fontSize: 12));
    return Column(
      children: accounts.map((a) => Container(
        margin: EdgeInsets.only(bottom: 12),
        padding: EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(25),
          border: Border.all(color: Colors.grey.shade100)
        ),
        child: Row(
          children: [
            Container(
              padding: EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.indigo.shade50, borderRadius: BorderRadius.circular(15)),
              child: Text(a['type'] == 'bank' ? '🏦' : '💵', style: TextStyle(fontSize: 20)),
            ),
            SizedBox(width: 15),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(a['name'], style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                Text(a['type'] == 'bank' ? 'حساب بنكي' : 'نقد / كاش', style: TextStyle(color: Colors.grey, fontSize: 10)),
              ],
            )),
            Text('${a['balance']} ${a['currency']}', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo, fontSize: 16)),
          ],
        ),
      )).toList(),
    );
  }

  Widget _buildHeaderCard() {
    final f = data?['fund'];
    return Container(
      padding: EdgeInsets.all(30),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.indigo.shade700, Colors.indigo.shade500]),
        borderRadius: BorderRadius.circular(40),
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.3), blurRadius: 20, offset: Offset(0, 10))]
      ),
      child: Column(
        children: [
          Text('إجمالي قيمة الصندوق', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.bold)),
          SizedBox(height: 10),
          Text('${f['current_value']} ${f['currency']}', style: TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900)),
          SizedBox(height: 20),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildSmallStat('رأس المال', '${f['capital']} ${f['currency']}'),
              _buildSmallStat('العملة', f['currency']),
              _buildSmallStat('التكرار', f['distribution_frequency']),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSmallStat(String label, String val) {
    return Column(
      children: [
        Text(label, style: TextStyle(color: Colors.white60, fontSize: 10, fontWeight: FontWeight.bold)),
        Text(val, style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
      ],
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.indigo.shade900));
  }

  Widget _buildEquitiesList() {
    final equities = data?['equities'] as List? ?? [];
    return Column(
      children: equities.map((e) => Container(
        margin: EdgeInsets.only(bottom: 15),
        padding: EdgeInsets.all(15),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100)),
        child: Row(
          children: [
            CircleAvatar(backgroundColor: Colors.indigo.shade50, child: Text(e['partner']['name'][0], style: TextStyle(color: Colors.indigo, fontWeight: FontWeight.bold))),
            SizedBox(width: 15),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(e['partner']['name'], style: TextStyle(fontWeight: FontWeight.bold)),
                Text(e['equity_type'] == 'contribution' ? 'مساهمة مالية' : 'نسبة ثابتة', style: TextStyle(color: Colors.grey, fontSize: 10)),
              ],
            )),
            Text('${e['percentage']}%', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo, fontSize: 16)),
          ],
        ),
      )).toList(),
    );
  }

  Widget _buildTransactionsList() {
    final txs = data?['recent_transactions'] as List? ?? [];
    if (txs.isEmpty) return Center(child: Text('لا توجد عمليات حالياً', style: TextStyle(color: Colors.grey)));
    return Column(
      children: txs.map((t) => Container(
        margin: EdgeInsets.only(bottom: 12),
        padding: EdgeInsets.all(15),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100)),
        child: Row(
          children: [
            Icon(t['type'] == 'income' ? Icons.add_circle : Icons.remove_circle, color: t['type'] == 'income' ? Colors.green : Colors.red),
            SizedBox(width: 15),
            Expanded(child: Text(t['description'] ?? t['category'], style: TextStyle(fontWeight: FontWeight.bold))),
            Text('${t['amount']} ${data?['fund']?['currency']}', style: TextStyle(fontWeight: FontWeight.w900, color: t['type'] == 'income' ? Colors.green : Colors.red)),
          ],
        ),
      )).toList(),
    );
  }
}
