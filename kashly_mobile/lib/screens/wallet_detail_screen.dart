import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../api/api_service.dart';

class WalletDetailScreen extends StatefulWidget {
  final int walletId;
  WalletDetailScreen({required this.walletId});

  @override
  _WalletDetailScreenState createState() => _WalletDetailScreenState();
}

class _WalletDetailScreenState extends State<WalletDetailScreen> {
  final apiService = ApiService();
  Map<String, dynamic>? data;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadDetail();
  }

  void loadDetail() async {
    final result = await apiService.getWalletDetail(widget.walletId);
    setState(() {
      data = result;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) return Scaffold(body: Center(child: CircularProgressIndicator(color: Colors.indigo)));
    final w = data;
    if (w == null) return Scaffold(body: Center(child: Text('فشل تحميل البيانات', style: GoogleFonts.almarai())));

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(w['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
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
              _buildSubAccountsSection(),
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
    final w = data;
    return Container(
      padding: EdgeInsets.all(30),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(color: Color(0xFF0F172A).withOpacity(0.15), blurRadius: 25, offset: Offset(0, 10))
        ]
      ),
      child: Column(
        children: [
          Text('الرصيد المتوفر في المحفظة', 
            style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontWeight: FontWeight.bold, fontSize: 13, letterSpacing: 0.5)),
          SizedBox(height: 12),
          Text('${w?['balance']} ${w?['currency']}', 
            style: GoogleFonts.outfit(color: Colors.white, fontSize: 38, fontWeight: FontWeight.w900, letterSpacing: -1.0)),
          SizedBox(height: 25),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildSmallStat('العملة الأساسية', w?['currency'] ?? 'USD'),
              SizedBox(width: 40),
              _buildSmallStat('الحالة', 'نشطة'),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSmallStat(String label, String val) {
    return Column(
      children: [
        Text(label, style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.4), fontSize: 11, fontWeight: FontWeight.bold)),
        SizedBox(height: 4),
        Text(val, style: GoogleFonts.outfit(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
      ],
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: GoogleFonts.almarai(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade900));
  }

  Widget _buildSubAccountsSection() {
    final subAccounts = data?['payment_methods'] as List? ?? [];
    if (subAccounts.isEmpty) return SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(height: 30),
        _buildSectionTitle('الحسابات والعهود الفرعية'),
        SizedBox(height: 15),
        ...subAccounts.map((sa) => Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Colors.grey.shade100, width: 1.5),
            boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))],
          ),
          child: Row(
            children: [
              Container(
                padding: EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.indigo.shade50,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  sa['type'] == 'bank' ? Icons.account_balance : Icons.person_outline,
                  color: Colors.indigo,
                  size: 22,
                ),
              ),
              SizedBox(width: 15),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      sa['name'],
                      style: GoogleFonts.almarai(
                        fontWeight: FontWeight.w900,
                        fontSize: 15,
                        color: Colors.blueGrey.shade900,
                      ),
                    ),
                    if (sa['custodian_name'] != null && sa['custodian_name'].toString().isNotEmpty)
                      Padding(
                        padding: const EdgeInsets.only(top: 4.0),
                        child: Text(
                          'عهدة: ${sa['custodian_name']}',
                          style: GoogleFonts.almarai(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: Colors.amber.shade800,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
              Text(
                '${sa['balance']} ${sa['currency']}',
                style: GoogleFonts.outfit(
                  fontWeight: FontWeight.w900,
                  fontSize: 16,
                  color: Colors.indigo.shade700,
                ),
              ),
            ],
          ),
        )).toList(),
      ],
    );
  }

  Widget _buildTransactionsList() {
    final txs = data?['transactions'] as List? ?? [];
    if (txs.isEmpty) return Center(child: Padding(
      padding: EdgeInsets.all(30),
      child: Text('لا توجد حركات لهذه المحفظة', style: GoogleFonts.almarai(color: Colors.blueGrey.shade300, fontSize: 14, fontWeight: FontWeight.bold)),
    ));
    return Column(
      children: txs.map((t) {
        final isIncome = t['type'] == 'income' || t['type'] == 'capital';
        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white, 
            borderRadius: BorderRadius.circular(24), 
            border: Border.all(color: Colors.grey.shade100, width: 1.5),
            boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))]
          ),
          child: Row(
            children: [
              Container(
                padding: EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: isIncome ? Colors.green.shade50 : Colors.red.shade50,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(isIncome ? Icons.arrow_downward : Icons.arrow_upward, 
                  color: isIncome ? Colors.green.shade600 : Colors.red.shade600, size: 20),
              ),
              SizedBox(width: 15),
              Expanded(child: Text(t['description'] ?? t['category'] ?? '', 
                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.blueGrey.shade800))),
              Text('${isIncome ? '+' : '-'}${t['amount']} ${data?['currency']}', 
                style: GoogleFonts.outfit(fontWeight: FontWeight.w900, fontSize: 16, color: isIncome ? Colors.green.shade600 : Colors.red.shade600)),
            ],
          ),
        );
      }).toList(),
    );
  }
}
