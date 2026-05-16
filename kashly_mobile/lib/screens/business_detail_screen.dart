import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'transfer_screen.dart';

class BusinessDetailScreen extends StatefulWidget {
  final int businessId;
  BusinessDetailScreen({required this.businessId});

  @override
  _BusinessDetailScreenState createState() => _BusinessDetailScreenState();
}

class _BusinessDetailScreenState extends State<BusinessDetailScreen> {
  final apiService = ApiService();
  Map<String, dynamic>? data;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadDetail();
  }

  void loadDetail() async {
    final result = await apiService.getBusinessDetail(widget.businessId);
    setState(() {
      data = result;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) return Scaffold(body: Center(child: CircularProgressIndicator()));
    final b = data;
    if (b == null) return Scaffold(body: Center(child: Text('فشل تحميل البيانات')));

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(b['name'], style: TextStyle(fontWeight: FontWeight.w900, color: Colors.amber.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: RefreshIndicator(
        onRefresh: () async => loadDetail(),
        child: SingleChildScrollView(
          padding: EdgeInsets.all(25),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildHeaderCard(),
              SizedBox(height: 35),
              _buildSectionTitle('الحركات الأخيرة'),
              SizedBox(height: 15),
              _buildTransactionsList(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderCard() {
    final b = data;
    return Container(
      padding: EdgeInsets.all(35),
      decoration: BoxDecoration(
        color: Colors.amber.shade50,
        borderRadius: BorderRadius.circular(45),
        border: Border.all(color: Colors.amber.shade200, width: 2),
        boxShadow: [BoxShadow(color: Colors.amber.shade100.withOpacity(0.5), blurRadius: 30, offset: Offset(0, 15))]
      ),
      child: Column(
        children: [
          Text('إجمالي قيمة النشاط التجاري', 
            style: TextStyle(color: Colors.amber.shade400, fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 0.5)),
          SizedBox(height: 15),
          Text('${b?['total_value']} ${b?['currency'] ?? 'USD'}', 
            style: TextStyle(color: Colors.amber.shade900, fontSize: 42, fontWeight: FontWeight.w900, letterSpacing: -1.0)),
          SizedBox(height: 25),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildSmallStat('الحالة', 'نشط'),
              SizedBox(width: 30),
              _buildSmallStat('العملة', b?['currency'] ?? 'USD'),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSmallStat(String label, String val) {
    return Column(
      children: [
        Text(label, style: TextStyle(color: Colors.amber.shade400, fontSize: 11, fontWeight: FontWeight.w900)),
        SizedBox(height: 4),
        Text(val, style: TextStyle(color: Colors.amber.shade900, fontSize: 18, fontWeight: FontWeight.w900)),
      ],
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade900));
  }

  Widget _buildTransactionsList() {
    final txs = data?['transactions'] as List? ?? [];
    if (txs.isEmpty) return Center(child: Padding(
      padding: EdgeInsets.all(30),
      child: Text('لا توجد حركات لهذا النشاط', style: TextStyle(color: Colors.blueGrey.shade300, fontSize: 14, fontWeight: FontWeight.bold)),
    ));
    return Column(
      children: txs.map((t) => Container(
        margin: EdgeInsets.only(bottom: 15),
        padding: EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(25), 
          border: Border.all(color: Colors.amber.shade100, width: 1.5)
        ),
        child: Row(
          children: [
            Icon(t['type'] == 'income' ? Icons.add_circle_outline : Icons.remove_circle_outline, 
              color: t['type'] == 'income' ? Colors.green : Colors.red, size: 28),
            SizedBox(width: 18),
            Expanded(child: Text(t['description'] ?? t['category'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.blueGrey.shade800))),
            Text('${t['type'] == 'income' ? '+' : '-'}${t['amount']} ${data?['currency'] ?? 'USD'}', 
              style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: t['type'] == 'income' ? Colors.green.shade600 : Colors.red.shade600)),
          ],
        ),
      )).toList(),
    );
  }
}
