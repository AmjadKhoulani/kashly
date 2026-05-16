import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:fl_chart/fl_chart.dart';
import '../api/api_service.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'businesses_screen.dart';
import 'wallets_screen.dart';
import 'business_detail_screen.dart';
import 'wallet_detail_screen.dart';
import 'fund_detail_screen.dart';
import 'funds_screen.dart';
import 'transactions_screen.dart';
import 'add_transaction_screen.dart';
import 'profile_screen.dart';

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
      backgroundColor: Color(0xFFF0F4F8),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: Colors.indigo)) 
        : RefreshIndicator(
            onRefresh: () async => loadData(),
            child: CustomScrollView(
              slivers: [
                _buildSliverAppBar(),
                SliverToBoxAdapter(
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        SizedBox(height: 25),
                        _buildMainWealthCard(currencyFormat),
                        SizedBox(height: 35),
                        _buildSectionHeader('نظرة عامة على السيولة', color: Colors.indigo),
                        SizedBox(height: 15),
                        _buildCashflowChart(),
                        SizedBox(height: 40),
                        _buildAssetSection('المحافظ الشخصية', WalletsScreen(), _buildWalletsList()),
                        SizedBox(height: 40),
                        _buildAssetSection('قطاع الأعمال', BusinessesScreen(), _buildBusinessesList()),
                        SizedBox(height: 40),
                        _buildAssetSection('الاستثمارات', FundsScreen(), _buildFundsList()),
                        SizedBox(height: 40),
                        _buildSectionHeader('آخر العمليات المالية', color: Colors.pink, onTap: () => Get.to(() => TransactionsScreen())),
                        SizedBox(height: 15),
                        _buildRecentTransactions(),
                        SizedBox(height: 100),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
      floatingActionButton: _buildPremiumFAB(),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 120,
      floating: true,
      pinned: true,
      backgroundColor: Color(0xFFF0F4F8),
      elevation: 0,
      centerTitle: false,
      title: Text('كاشلي.', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.indigo.shade900, fontSize: 26)),
      actions: [
        IconButton(
          icon: Container(
            padding: EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, border: Border.all(color: Colors.indigo.shade50)),
            child: Icon(Icons.person_outline, color: Colors.indigo.shade900, size: 22),
          ),
          onPressed: () => Get.to(() => ProfileScreen()),
        ),
        SizedBox(width: 10),
      ],
    );
  }

  Widget _buildMainWealthCard(NumberFormat format) {
    return Container(
      height: 220,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Colors.indigo.shade700, Colors.indigo.shade400],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(40),
        boxShadow: [
          BoxShadow(color: Colors.indigo.shade200.withOpacity(0.6), blurRadius: 30, offset: Offset(0, 15)),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -30,
            top: -30,
            child: CircleAvatar(radius: 80, backgroundColor: Colors.white.withOpacity(0.1)),
          ),
          Padding(
            padding: EdgeInsets.all(35),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('إجمالي الثروة التقديري', style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.8), fontWeight: FontWeight.bold, fontSize: 14)),
                    Icon(Icons.auto_graph, color: Colors.white.withOpacity(0.8), size: 20),
                  ],
                ),
                SizedBox(height: 10),
                Text(format.format(data?['estimated_total_usd'] ?? 0), 
                  style: GoogleFonts.outfit(color: Colors.white, fontSize: 48, fontWeight: FontWeight.w900, letterSpacing: -2)),
                Spacer(),
                Row(
                  children: (data?['total_by_currency'] is Map ? (data?['total_by_currency'] as Map) : {}).entries.where((e) => e.key != 'USD').take(3).map((e) => Container(
                    margin: EdgeInsets.only(left: 10),
                    padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(12)),
                    child: Text('${e.value} ${e.key}', style: GoogleFonts.almarai(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                  )).toList(),
                )
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAssetSection(String title, Widget screen, Widget list) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildSectionHeader(title, color: Colors.indigo, onTap: () => Get.to(() => screen)),
        SizedBox(height: 20),
        list,
      ],
    );
  }

  Widget _buildSectionHeader(String title, {Color color = Colors.indigo, VoidCallback? onTap}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title, style: GoogleFonts.almarai(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade900)),
        if (onTap != null)
          TextButton(
            onPressed: onTap,
            child: Text('مشاهدة الكل', style: GoogleFonts.almarai(color: Colors.indigo.shade400, fontWeight: FontWeight.bold, fontSize: 14)),
          ),
      ],
    );
  }

  Widget _buildWalletsList() {
    final wallets = data?['wallets'] as List? ?? [];
    if (wallets.isEmpty) return _buildEmptyAsset('لا توجد محافظ');
    return Container(
      height: 160,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: wallets.length,
        itemBuilder: (context, i) {
          final w = wallets[i];
          return GestureDetector(
            onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id'])),
            child: Container(
              width: 200,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: Colors.white, 
                borderRadius: BorderRadius.circular(30), 
                border: Border.all(color: Colors.white, width: 2),
                boxShadow: [BoxShadow(color: Colors.indigo.shade50.withOpacity(0.5), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.account_balance_wallet, color: Colors.indigo.shade300, size: 28),
                  Spacer(),
                  Text(w['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15, color: Colors.blueGrey.shade800)),
                  SizedBox(height: 4),
                  Text('${w['balance']} ${w['currency']}', style: GoogleFonts.outfit(color: Colors.indigo.shade600, fontWeight: FontWeight.w900, fontSize: 22)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildBusinessesList() {
    final businesses = data?['businesses'] as List? ?? [];
    if (businesses.isEmpty) return _buildEmptyAsset('لا توجد أعمال');
    return Container(
      height: 160,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: businesses.length,
        itemBuilder: (context, i) {
          final b = businesses[i];
          return GestureDetector(
            onTap: () => Get.to(() => BusinessDetailScreen(businessId: b['id'])),
            child: Container(
              width: 200,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: Colors.amber.shade50, 
                borderRadius: BorderRadius.circular(30), 
                boxShadow: [BoxShadow(color: Colors.amber.shade100.withOpacity(0.3), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.storefront, color: Colors.amber.shade700, size: 28),
                  Spacer(),
                  Text(b['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15, color: Colors.amber.shade900)),
                  SizedBox(height: 4),
                  Text('${b['total_value']} ${b['currency'] ?? 'USD'}', style: GoogleFonts.outfit(color: Colors.amber.shade700, fontWeight: FontWeight.w900, fontSize: 22)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildFundsList() {
    final fundsList = data?['funds'] as List? ?? [];
    if (fundsList.isEmpty) return _buildEmptyAsset('لا توجد استثمارات');
    return Container(
      height: 160,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: fundsList.length,
        itemBuilder: (context, i) {
          final f = fundsList[i];
          return GestureDetector(
            onTap: () => Get.to(() => FundDetailScreen(fundId: f['id'])),
            child: Container(
              width: 200,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: Colors.green.shade50, 
                borderRadius: BorderRadius.circular(30), 
                boxShadow: [BoxShadow(color: Colors.green.shade100.withOpacity(0.3), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(f['icon'] ?? '📈', style: TextStyle(fontSize: 24)),
                  Spacer(),
                  Text(f['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15, color: Colors.green.shade900)),
                  SizedBox(height: 4),
                  Text('${f['current_value']} ${f['currency']}', style: GoogleFonts.outfit(color: Colors.green.shade700, fontWeight: FontWeight.w900, fontSize: 22)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildEmptyAsset(String text) {
    return Container(
      padding: EdgeInsets.all(30),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(30), border: Border.all(color: Colors.indigo.shade50, width: 2)),
      child: Center(child: Text(text, style: GoogleFonts.almarai(color: Colors.blueGrey.shade200, fontWeight: FontWeight.bold))),
    );
  }

  Widget _buildRecentTransactions() {
    final txs = data?['recent_transactions'] as List? ?? [];
    if (txs.isEmpty) return _buildEmptyAsset('لا توجد عمليات حديثة');
    return Column(
      children: txs.map((t) {
        final categoryRaw = t['category'];
        final category = categoryRaw is Map ? categoryRaw : null;
        final categoryName = category?['name'] ?? 'بدون تصنيف';
        final categoryIcon = category?['icon'] ?? '💸';
        final categoryColor = category?['color'] ?? '#94a3b8';
        final amount = t['amount']?.toString() ?? '0';
        final type = t['type'] ?? 'expense';
        final description = t['description'] ?? categoryName;
        
        final paymentMethodRaw = t['payment_method'];
        final paymentMethod = paymentMethodRaw is Map ? paymentMethodRaw : null;
        final currency = paymentMethod?['currency'] ?? (t['currency'] ?? '');

        return Container(
          margin: EdgeInsets.only(bottom: 15),
          padding: EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white, 
            borderRadius: BorderRadius.circular(30),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: Offset(0, 5))]
          ),
          child: Row(
            children: [
              Container(
                width: 55, height: 55,
                decoration: BoxDecoration(
                  color: Color(int.parse(categoryColor.replaceFirst('#', '0xFF'))).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Center(child: Text(categoryIcon, style: TextStyle(fontSize: 24))),
              ),
              SizedBox(width: 15),
              Expanded(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(description, style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.blueGrey.shade900)),
                  SizedBox(height: 2),
                  Text(categoryName, style: GoogleFonts.almarai(color: Colors.blueGrey.shade300, fontSize: 12, fontWeight: FontWeight.bold)),
                ],
              )),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('${type == 'expense' ? '-' : '+'}$amount', 
                    style: GoogleFonts.outfit(
                      fontWeight: FontWeight.w900, 
                      fontSize: 18, 
                      color: type == 'income' ? Colors.green.shade600 : (type == 'capital' ? Colors.amber.shade700 : Colors.pink.shade600)
                    )
                  ),
                  Text(currency, style: GoogleFonts.almarai(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade200)),
                ],
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildCashflowChart() {
    if (data == null || data?['chart_data'] == null) return SizedBox();
    
    final chartDataRaw = data?['chart_data'];
    if (chartDataRaw is! Map) return _buildEmptyAsset('بيانات الرسم البياني غير متوفرة');
    
    final chartData = chartDataRaw;
    final List<double> commercial = List<double>.from(chartData['commercial'] ?? []);
    final List<double> personal = List<double>.from(chartData['personal'] ?? []);
    final List<String> days = List<String>.from(chartData['days'] ?? []);

    return Container(
      height: 280,
      padding: EdgeInsets.fromLTRB(10, 25, 25, 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(40),
        boxShadow: [BoxShadow(color: Colors.indigo.shade50.withOpacity(0.4), blurRadius: 30, offset: Offset(0, 10))],
      ),
      child: LineChart(
        LineChartData(
          gridData: FlGridData(show: true, drawVerticalLine: false, getDrawingHorizontalLine: (value) => FlLine(color: Colors.indigo.shade50, strokeWidth: 1)),
          titlesData: FlTitlesData(
            show: true,
            rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
            topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                reservedSize: 30,
                interval: 1,
                getTitlesWidget: (value, meta) {
                  int index = value.toInt();
                  if (index >= 0 && index < days.length) {
                    return Text(days[index], style: GoogleFonts.almarai(color: Colors.indigo.shade200, fontWeight: FontWeight.bold, fontSize: 10));
                  }
                  return Text('');
                },
              ),
            ),
            leftTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                interval: 5000,
                getTitlesWidget: (value, meta) {
                  if (value == 0) return Text('0', style: GoogleFonts.outfit(color: Colors.indigo.shade100, fontSize: 10));
                  return Text('${(value / 1000).toStringAsFixed(0)}k', style: GoogleFonts.outfit(color: Colors.indigo.shade200, fontWeight: FontWeight.bold, fontSize: 10));
                },
                reservedSize: 35,
              ),
            ),
          ),
          borderData: FlBorderData(show: false),
          lineBarsData: [
            LineChartBarData(
              spots: List.generate(commercial.length, (i) => FlSpot(i.toDouble(), commercial[i])),
              isCurved: true,
              color: Colors.amber.shade400,
              barWidth: 5,
              isStrokeCapRound: true,
              dotData: FlDotData(show: false),
              belowBarData: BarAreaData(show: true, gradient: LinearGradient(colors: [Colors.amber.shade400.withOpacity(0.2), Colors.amber.shade400.withOpacity(0.0)], begin: Alignment.topCenter, end: Alignment.bottomCenter)),
            ),
            LineChartBarData(
              spots: List.generate(personal.length, (i) => FlSpot(i.toDouble(), personal[i])),
              isCurved: true,
              color: Colors.indigo.shade500,
              barWidth: 5,
              isStrokeCapRound: true,
              dotData: FlDotData(show: false),
              belowBarData: BarAreaData(show: true, gradient: LinearGradient(colors: [Colors.indigo.shade500.withOpacity(0.2), Colors.indigo.shade500.withOpacity(0.0)], begin: Alignment.topCenter, end: Alignment.bottomCenter)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPremiumFAB() {
    return GestureDetector(
      onTap: () async {
        final refresh = await Get.to(() => AddTransactionScreen());
        if (refresh == true) loadData();
      },
      child: Container(
        width: 180,
        height: 65,
        decoration: BoxDecoration(
          color: Colors.indigo.shade900,
          borderRadius: BorderRadius.circular(30),
          boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.3), blurRadius: 20, offset: Offset(0, 10))],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.add, color: Colors.white, size: 28),
            SizedBox(width: 12),
            Text('تسجيل حركة', style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 18)),
          ],
        ),
      ),
    );
  }
}

