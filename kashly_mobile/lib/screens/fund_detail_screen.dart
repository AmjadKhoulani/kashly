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
        Expanded(child: _actionButton('تحويل', Icons.swap_horiz, Colors.amber.shade700, () async {
          final res = await Get.to(() => TransferScreen(
            sourceId: widget.fundId, 
            sourceType: 'InvestmentFund',
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
        padding: EdgeInsets.symmetric(vertical: 22),
        decoration: BoxDecoration(
          color: color.withOpacity(0.12),
          borderRadius: BorderRadius.circular(30),
          border: Border.all(color: color.withOpacity(0.2), width: 1.5)
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 32),
            SizedBox(height: 10),
            Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w900, fontSize: 14)),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountsList() {
    final accounts = data?['payment_methods'] as List? ?? [];
    if (accounts.isEmpty) return Text('لا توجد حسابات مضافة', style: TextStyle(color: Colors.blueGrey.shade300, fontSize: 14, fontWeight: FontWeight.bold));
    return Column(
      children: accounts.map((a) => Container(
        margin: EdgeInsets.only(bottom: 15),
        padding: EdgeInsets.all(22),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(30),
          border: Border.all(color: Colors.blueGrey.shade100, width: 1.5),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10)]
        ),
        child: Row(
          children: [
            Container(
              padding: EdgeInsets.all(12),
              decoration: BoxDecoration(color: Colors.indigo.shade50, borderRadius: BorderRadius.circular(18)),
              child: Text(a['type'] == 'bank' ? '🏦' : '💵', style: TextStyle(fontSize: 24)),
            ),
            SizedBox(width: 18),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(a['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.blueGrey.shade800)),
                Text(a['type'] == 'bank' ? 'حساب بنكي نشط' : 'نقد / كاش', style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 12, fontWeight: FontWeight.w900)),
              ],
            )),
            Text('${a['balance']} ${a['currency']}', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo, fontSize: 20)),
          ],
        ),
      )).toList(),
    );
  }

  Widget _buildHeaderCard() {
    final f = data?['fund'];
    return Container(
      padding: EdgeInsets.all(35),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Colors.indigo.shade800, Colors.indigo.shade600]
        ),
        borderRadius: BorderRadius.circular(45),
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.4), blurRadius: 30, offset: Offset(0, 15))]
      ),
      child: Column(
        children: [
          Text('إجمالي قيمة الصندوق التقديرية', 
            style: TextStyle(color: Colors.white70, fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 0.5)),
          SizedBox(height: 15),
          Text('${f['current_value']} ${f['currency']}', 
            style: TextStyle(color: Colors.white, fontSize: 42, fontWeight: FontWeight.w900, letterSpacing: -1.0)),
          SizedBox(height: 25),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildSmallStat('رأس المال', '${f['capital']} ${f['currency']}'),
              _buildSmallStat('العملة الأساسية', f['currency']),
              _buildSmallStat('تكرار التوزيع', f['distribution_frequency']),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSmallStat(String label, String val) {
    return Column(
      children: [
        Text(label, style: TextStyle(color: Colors.white60, fontSize: 11, fontWeight: FontWeight.w900)),
        SizedBox(height: 4),
        Text(val, style: TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.w900)),
      ],
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Colors.indigo.shade900));
  }

  Widget _buildEquitiesList() {
    final equities = data?['equities'] as List? ?? [];
    return Column(
      children: equities.map((e) => Container(
        margin: EdgeInsets.only(bottom: 18),
        padding: EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(25), 
          border: Border.all(color: Colors.blueGrey.shade100, width: 1.5)
        ),
        child: Row(
          children: [
            Container(
              width: 50, height: 50,
              decoration: BoxDecoration(color: Colors.indigo.shade50, borderRadius: BorderRadius.circular(15)),
              child: Center(child: Text(e['partner']['name'][0], style: TextStyle(color: Colors.indigo, fontWeight: FontWeight.w900, fontSize: 20))),
            ),
            SizedBox(width: 18),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(e['partner']['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 17, color: Colors.blueGrey.shade800)),
                Text(e['equity_type'] == 'contribution' ? 'مساهمة مالية نشطة' : 'نسبة ثابتة متفق عليها', style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 12, fontWeight: FontWeight.bold)),
              ],
            )),
            Container(
              padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(color: Colors.indigo.withOpacity(0.05), borderRadius: BorderRadius.circular(10)),
              child: Text('${e['percentage']}%', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo, fontSize: 18)),
            ),
          ],
        ),
      )).toList(),
    );
  }

  Widget _buildTransactionsList() {
    final txs = data?['recent_transactions'] as List? ?? [];
    if (txs.isEmpty) return Center(child: Padding(
      padding: EdgeInsets.all(30),
      child: Text('لا توجد عمليات حالياً لهذا الصندوق', style: TextStyle(color: Colors.blueGrey.shade300, fontSize: 14, fontWeight: FontWeight.bold)),
    ));
    return Column(
      children: txs.map((t) => Container(
        margin: EdgeInsets.only(bottom: 15),
        padding: EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(25), 
          border: Border.all(color: Colors.blueGrey.shade100, width: 1.5)
        ),
        child: Row(
          children: [
            Icon(t['type'] == 'income' ? Icons.add_circle_outline : Icons.remove_circle_outline, 
              color: t['type'] == 'income' ? Colors.green : Colors.red, size: 28),
            SizedBox(width: 18),
            Expanded(child: Text(t['description'] ?? t['category'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.blueGrey.shade800))),
            Text('${t['type'] == 'income' ? '+' : '-'}${t['amount']} ${data?['fund']?['currency']}', 
              style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: t['type'] == 'income' ? Colors.green.shade600 : Colors.red.shade600)),
          ],
        ),
      )).toList(),
    );
  }
}
