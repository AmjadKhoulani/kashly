import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'package:intl/intl.dart';

class DashboardScreen extends StatefulWidget {
  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final apiService = ApiService();
  Map<String, dynamic>? data;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  void loadData() async {
    final result = await apiService.getDashboard();
    setState(() {
      data = result;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat.currency(symbol: '\$', decimalDigits: 0);

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('إمبراطوريتي المالية', style: TextStyle(fontWeight: FontWeight.black, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        actions: [
          IconButton(
            icon: Icon(Icons.logout, color: Colors.indigo.shade900),
            onPressed: () async {
              await apiService.logout();
              Get.offAllNamed('/login');
            },
          )
        ],
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : RefreshIndicator(
            onRefresh: () async => loadData(),
            child: SingleChildScrollView(
              padding: EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  _buildWealthCard(currencyFormat),
                  SizedBox(height: 30),
                  _buildSectionTitle('المحافظ الشخصية'),
                  SizedBox(height: 15),
                  _buildWalletsList(),
                  SizedBox(height: 30),
                  _buildSectionTitle('آخر العمليات'),
                  SizedBox(height: 15),
                  _buildRecentTransactions(),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildWealthCard(NumberFormat format) {
    return Container(
      padding: EdgeInsets.all(30),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.indigo.shade700, Colors.indigo.shade500]),
        borderRadius: BorderRadius.circular(40),
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.3), blurRadius: 20, offset: Offset(0, 10))],
      ),
      child: Column(
        children: [
          Text('إجمالي الثروة التقديرية', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.bold, fontSize: 14)),
          SizedBox(height: 10),
          Text(format.format(data?['estimated_total_usd'] ?? 0), 
            style: TextStyle(color: Colors.white, fontSize: 42, fontWeight: FontWeight.black, letterSpacing: -2)),
          SizedBox(height: 20),
          Wrap(
            spacing: 10,
            children: (data?['total_by_currency'] as Map).entries.map((e) => Container(
              padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), borderRadius: BorderRadius.circular(12)),
              child: Text('${e.value} ${e.key}', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
            )).toList(),
          )
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: TextStyle(fontSize: 20, fontWeight: FontWeight.black, color: Colors.indigo.shade900)),
        Text('الكل', style: TextStyle(color: Colors.indigo, fontWeight: FontWeight.bold, fontSize: 12)),
      ],
    );
  }

  Widget _buildWalletsList() {
    final wallets = data?['wallets'] as List? ?? [];
    return Container(
      height: 100,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: wallets.length,
        itemBuilder: (context, i) {
          final w = wallets[i];
          return Container(
            width: 160,
            margin: EdgeInsets.only(left: 15),
            padding: EdgeInsets.all(15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25), border: Border.all(color: Colors.indigo.withOpacity(0.05))),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(w['name'], style: TextStyle(fontWeight: FontWeight.black, fontSize: 14)),
                Text('\$${w['balance']}', style: TextStyle(color: Colors.indigo, fontWeight: FontWeight.black, fontSize: 18)),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildRecentTransactions() {
    final txs = data?['recent_transactions'] as List? ?? [];
    return Column(
      children: txs.map((t) => Container(
        margin: EdgeInsets.only(bottom: 15),
        padding: EdgeInsets.all(15),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25)),
        child: Row(
          children: [
            Container(
              width: 50, h: 50,
              decoration: BoxDecoration(color: t['type'] == 'income' ? Colors.emerald.withOpacity(0.1) : Colors.rose.withOpacity(0.1), borderRadius: BorderRadius.circular(15)),
              child: Icon(t['type'] == 'income' ? Icons.trending_up : Icons.trending_down, color: t['type'] == 'income' ? Colors.emerald : Colors.rose),
            ),
            SizedBox(width: 15),
            Expanded(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(t['description'] ?? t['category'], style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                Text(t['category'], style: TextStyle(color: Colors.grey, fontSize: 10)),
              ],
            )),
            Text('${t['type'] == 'income' ? '+' : '-'}\$${t['amount']}', 
              style: TextStyle(fontWeight: FontWeight.black, color: t['type'] == 'income' ? Colors.emerald : Colors.rose)),
          ],
        ),
      )).toList(),
    );
  }
}
