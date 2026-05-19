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
          colors: [Color(0xFF1E1B4B), Color(0xFF312E81), Color(0xFF4338CA)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(color: Color(0xFF4338CA).withOpacity(0.3), blurRadius: 25, offset: Offset(0, 12)),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: Stack(
          children: [
            Positioned(
              right: -50,
              bottom: -50,
              child: CircleAvatar(
                radius: 120,
                backgroundColor: Colors.white.withOpacity(0.03),
              ),
            ),
            Positioned(
              left: -30,
              top: -30,
              child: CircleAvatar(
                radius: 80,
                backgroundColor: Colors.white.withOpacity(0.02),
              ),
            ),
            Padding(
              padding: EdgeInsets.all(30),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('إجمالي الثروة التقديري', 
                        style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.7), fontWeight: FontWeight.bold, fontSize: 13, letterSpacing: 0.5)),
                      Container(
                        padding: EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(Icons.account_balance, color: Colors.amber.shade400, size: 18),
                      ),
                    ],
                  ),
                  SizedBox(height: 10),
                  Text(format.format(data?['estimated_total_usd'] ?? 0), 
                    style: GoogleFonts.outfit(color: Colors.white, fontSize: 44, fontWeight: FontWeight.w900, letterSpacing: -1.5)),
                  Spacer(),
                  Text('العملات الأخرى المتوفرة:', 
                    style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 11, fontWeight: FontWeight.bold)),
                  SizedBox(height: 8),
                  Row(
                    children: (data?['total_by_currency'] is Map ? (data?['total_by_currency'] as Map) : {}).entries.where((e) => e.key != 'USD').take(3).map((e) => Container(
                      margin: EdgeInsets.only(left: 8),
                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1), 
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.white.withOpacity(0.05)),
                      ),
                      child: Text('${e.value} ${e.key}', style: GoogleFonts.outfit(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w800)),
                    )).toList(),
                  )
                ],
              ),
            ),
          ],
        ),
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
      height: 170,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: wallets.length,
        itemBuilder: (context, i) {
          final w = wallets[i];
          return GestureDetector(
            onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id'])),
            child: Container(
              width: 220,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(22),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.white, Color(0xFFF8FAFC)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(28), 
                border: Border.all(color: Colors.grey.shade100, width: 1.5),
                boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.03), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.indigo.shade50,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Icon(Icons.account_balance_wallet, color: Colors.indigo.shade600, size: 22),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          w['currency'] ?? 'USD',
                          style: GoogleFonts.outfit(color: Colors.green.shade700, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(w['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.blueGrey.shade900)),
                  SizedBox(height: 6),
                  Text('${w['balance']} ${w['currency']}', style: GoogleFonts.outfit(color: Colors.indigo.shade700, fontWeight: FontWeight.w900, fontSize: 20)),
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
      height: 170,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: businesses.length,
        itemBuilder: (context, i) {
          final b = businesses[i];
          return GestureDetector(
            onTap: () => Get.to(() => BusinessDetailScreen(businessId: b['id'])),
            child: Container(
              width: 220,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(22),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.amber.shade50, Colors.amber.shade100.withOpacity(0.5)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(28), 
                border: Border.all(color: Colors.amber.shade100, width: 1.5),
                boxShadow: [BoxShadow(color: Colors.amber.shade900.withOpacity(0.02), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Icon(Icons.storefront, color: Colors.amber.shade800, size: 22),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.amber.shade800,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          b['currency'] ?? 'USD',
                          style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(b['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.amber.shade900)),
                  SizedBox(height: 6),
                  Text('${b['total_value']} ${b['currency'] ?? 'USD'}', style: GoogleFonts.outfit(color: Colors.amber.shade900, fontWeight: FontWeight.w900, fontSize: 20)),
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
      height: 170,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: fundsList.length,
        itemBuilder: (context, i) {
          final f = fundsList[i];
          return GestureDetector(
            onTap: () => Get.to(() => FundDetailScreen(fundId: f['id'])),
            child: Container(
              width: 220,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(22),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.green.shade50, Colors.green.shade100.withOpacity(0.5)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(28), 
                border: Border.all(color: Colors.green.shade100, width: 1.5),
                boxShadow: [BoxShadow(color: Colors.green.shade900.withOpacity(0.02), blurRadius: 20, offset: Offset(0, 10))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Text(f['icon'] ?? '📈', style: TextStyle(fontSize: 22)),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade700,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          f['currency'] ?? 'USD',
                          style: GoogleFonts.outfit(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(f['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.green.shade900)),
                  SizedBox(height: 6),
                  Text('${f['current_value']} ${f['currency']}', style: GoogleFonts.outfit(color: Colors.green.shade800, fontWeight: FontWeight.w900, fontSize: 20)),
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
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(28), 
        border: Border.all(color: Colors.grey.shade100, width: 1.5),
        boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Center(child: Text(text, style: GoogleFonts.almarai(color: Colors.blueGrey.shade200, fontWeight: FontWeight.bold))),
    );
  }

  Widget _buildRecentTransactions() {
    final txs = data?['recent_transactions'] as List? ?? [];
    if (txs.isEmpty) return _buildEmptyAsset('لا توجد عمليات حديثة');
    return Column(
      children: txs.map((t) {
        final categoryRelationRaw = t['category_relation'];
        final category = categoryRelationRaw is Map ? categoryRelationRaw : null;
        
        final String categoryColor = (category != null && category['color'] != null) 
            ? category['color'].toString() 
            : '#6366f1';
        final String categoryIcon = (category != null && category['icon'] != null) 
            ? category['icon'].toString() 
            : (t['type'] == 'income' ? '↓' : '↑');
        final String categoryName = (category != null && category['name'] != null) 
            ? category['name'].toString() 
            : (t['category']?.toString() ?? 'بدون تصنيف');
        final String description = t['description']?.toString() ?? categoryName;
        final String amount = t['amount']?.toString() ?? '0.00';
        final String type = t['type'] ?? 'expense';
        
        final String currency = (t['payment_method'] != null && t['payment_method']['currency'] != null)
            ? t['payment_method']['currency'].toString()
            : (t['currency']?.toString() ?? '');

        Color iconColor = Color(int.parse(categoryColor.replaceFirst('#', '0xFF')));
        Color typeColor = type == 'income' ? Colors.green.shade600 : (type == 'capital' ? Colors.amber.shade700 : Colors.red.shade600);

        return Container(
          margin: EdgeInsets.only(bottom: 14),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white, 
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Colors.grey.shade50, width: 1.5),
            boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))]
          ),
          child: Row(
            children: [
              Container(
                width: 52, height: 52,
                decoration: BoxDecoration(
                  color: iconColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Center(child: Text(categoryIcon, style: TextStyle(fontSize: 22))),
              ),
              SizedBox(width: 15),
              Expanded(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(description, style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15, color: Colors.blueGrey.shade900)),
                  SizedBox(height: 3),
                  Text(categoryName, style: GoogleFonts.almarai(color: Colors.blueGrey.shade300, fontSize: 11, fontWeight: FontWeight.bold)),
                ],
              )),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('${type == 'income' ? '+' : '-'}$amount', 
                    style: GoogleFonts.outfit(
                      fontWeight: FontWeight.w900, 
                      fontSize: 17, 
                      color: typeColor,
                    )
                  ),
                  SizedBox(height: 3),
                  Text(currency, style: GoogleFonts.outfit(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.blueGrey.shade300)),
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
    final List<double> commercial = (chartData['commercial'] as List? ?? [])
        .map((e) => (e as num).toDouble())
        .toList();
    final List<double> personal = (chartData['personal'] as List? ?? [])
        .map((e) => (e as num).toDouble())
        .toList();
    final List<String> days = List<String>.from(chartData['days'] ?? []);

    return Container(
      height: 280,
      padding: EdgeInsets.fromLTRB(10, 25, 25, 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(40),
        border: Border.all(color: Colors.grey.shade50, width: 1.5),
        boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 30, offset: Offset(0, 10))],
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
              barWidth: 4,
              isStrokeCapRound: true,
              dotData: FlDotData(show: false),
              belowBarData: BarAreaData(show: true, gradient: LinearGradient(colors: [Colors.amber.shade400.withOpacity(0.15), Colors.amber.shade400.withOpacity(0.0)], begin: Alignment.topCenter, end: Alignment.bottomCenter)),
            ),
            LineChartBarData(
              spots: List.generate(personal.length, (i) => FlSpot(i.toDouble(), personal[i])),
              isCurved: true,
              color: Colors.indigo.shade500,
              barWidth: 4,
              isStrokeCapRound: true,
              dotData: FlDotData(show: false),
              belowBarData: BarAreaData(show: true, gradient: LinearGradient(colors: [Colors.indigo.shade500.withOpacity(0.15), Colors.indigo.shade500.withOpacity(0.0)], begin: Alignment.topCenter, end: Alignment.bottomCenter)),
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
        width: 200,
        height: 60,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF4338CA), Color(0xFF6366F1)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(30),
          boxShadow: [BoxShadow(color: Color(0xFF6366F1).withOpacity(0.35), blurRadius: 20, offset: Offset(0, 10))],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.add, color: Colors.white, size: 24),
            SizedBox(width: 8),
            Text('تسجيل حركة جديدة', style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16)),
          ],
        ),
      ),
    );
  }
}

