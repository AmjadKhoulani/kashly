import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:fl_chart/fl_chart.dart';
import '../api/api_service.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';

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
      backgroundColor: Color(0xFFF4F6F9),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: Colors.indigo)) 
        : RefreshIndicator(
            onRefresh: () async => loadData(),
            child: CustomScrollView(
              physics: BouncingScrollPhysics(),
              slivers: [
                _buildSliverAppBar(),
                SliverToBoxAdapter(
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        SizedBox(height: 15),
                        
                        // Unified 6-Card Stats Grid (Estimated Net Worth, Cash, Business, Receivables, Payables, Net Debt)
                        _buildStatsGrid(currencyFormat),
                        SizedBox(height: 30),
                        
                        // Upcoming Payment Reminders Section
                        _buildUpcomingDebtsSection(currencyFormat),
                        SizedBox(height: 30),
                        
                        _buildSectionHeader('نظرة عامة على السيولة'),
                        SizedBox(height: 15),
                        _buildCashflowChart(),
                        SizedBox(height: 35),
                        
                        _buildAssetSection(
                          'المحافظ الشخصية', 
                          trailingValue: '\$${NumberFormat('#,##0').format(double.tryParse(data?['estimated_personal_cash_usd']?.toString() ?? '0') ?? 0.0)}',
                          screen: WalletsScreen(), 
                          list: _buildWalletsList()
                        ),
                        SizedBox(height: 35),
                        
                        _buildAssetSection(
                          'الاستثمارات', 
                          trailingValue: '\$${NumberFormat('#,##0').format((double.tryParse(data?['estimated_business_only_usd']?.toString() ?? '0') ?? 0.0) + (double.tryParse(data?['estimated_funds_only_usd']?.toString() ?? '0') ?? 0.0))}',
                          screen: FundsScreen(), 
                          list: _buildFundsList()
                        ),
                        SizedBox(height: 35),
                        
                        _buildSectionHeader(
                          'آخر العمليات المالية', 
                          onTap: () => Get.to(() => TransactionsScreen())
                        ),
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
      floatingActionButton: null,
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 100,
      floating: true,
      pinned: true,
      backgroundColor: Color(0xFFF4F6F9),
      elevation: 0,
      centerTitle: false,
      title: Text('كاشلي.', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.indigo.shade900, fontSize: 26)),
      actions: [
        IconButton(
          icon: Container(
            padding: EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white, 
              shape: BoxShape.circle, 
              border: Border.all(color: Color(0xFFE2E8F0)),
              boxShadow: [
                BoxShadow(color: Color(0xFF0F172A).withOpacity(0.015), blurRadius: 10)
              ]
            ),
            child: Icon(Icons.person_outline, color: Colors.indigo.shade900, size: 20),
          ),
          onPressed: () => Get.to(() => ProfileScreen()),
        ),
        SizedBox(width: 15),
      ],
    );
  }

  Widget _buildStatsGrid(NumberFormat format) {
    final double estimatedTotalUSD = double.tryParse(data?['estimated_total_usd']?.toString() ?? '0') ?? 0.0;
    final double estimatedPersonalCashUSD = double.tryParse(data?['estimated_personal_cash_usd']?.toString() ?? '0') ?? 0.0;
    final double estimatedBusinessValueUSD = double.tryParse(data?['estimated_business_value_usd']?.toString() ?? '0') ?? 0.0;
    final double estimatedBusinessOnlyUSD = double.tryParse(data?['estimated_business_only_usd']?.toString() ?? '0') ?? 0.0;
    final double estimatedFundsOnlyUSD = double.tryParse(data?['estimated_funds_only_usd']?.toString() ?? '0') ?? 0.0;
    final double receivables = double.tryParse(data?['total_receivables_usd']?.toString() ?? '0') ?? 0.0;
    final double payables = double.tryParse(data?['total_payables_usd']?.toString() ?? '0') ?? 0.0;
    final double netDebts = double.tryParse(data?['net_debts_usd']?.toString() ?? '0') ?? 0.0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // 1. Wealth Card (Full Width)
        _buildWealthCard(estimatedTotalUSD, format),
        SizedBox(height: 12),
        
        // 2-Column Grid for the 4 core KPI cards
        Row(
          children: [
            Expanded(
              child: _buildGradientKPI(
                title: 'النقد الشخصي المتوفر',
                amount: estimatedPersonalCashUSD,
                format: format,
                colors: [Color(0xFFF0F9FF), Color(0xFFE0F2FE)],
                borderColor: Color(0xFFBAE6FD),
                textColor: Color(0xFF0369A1),
                subtext: 'محافظك الشخصية المجمعة',
                icon: Icons.account_balance_wallet,
              ),
            ),
            SizedBox(width: 12),
            Expanded(
              child: _buildGradientKPI(
                title: 'المشاريع والاستثمارات',
                amount: estimatedBusinessValueUSD,
                format: format,
                colors: [Color(0xFFFEF3C7), Color(0xFFFDE68A)],
                borderColor: Color(0xFFFCD34D),
                textColor: Color(0xFFB45309),
                subtext: 'مشاريع: \$${NumberFormat('#,##0').format(estimatedBusinessOnlyUSD)} | صناديق: \$${NumberFormat('#,##0').format(estimatedFundsOnlyUSD)}',
                icon: Icons.storefront,
              ),
            ),
          ],
        ),
        SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _buildGradientKPI(
                title: 'ديون لي (مستحقات)',
                amount: receivables,
                format: format,
                colors: [Color(0xFFECFDF5), Color(0xFFD1FAE5)],
                borderColor: Color(0xFFA7F3D0),
                textColor: Color(0xFF047857),
                subtext: 'مستحقاتك بذمة الآخرين',
                icon: Icons.arrow_downward,
              ),
            ),
            SizedBox(width: 12),
            Expanded(
              child: _buildGradientKPI(
                title: 'ديون عليّ (التزامات)',
                amount: payables,
                format: format,
                colors: [Color(0xFFFFF1F2), Color(0xFFFFE4E6)],
                borderColor: Color(0xFFFECDD3),
                textColor: Color(0xFFBE123C),
                subtext: 'قروض وأقساط مستحقة سداد',
                icon: Icons.arrow_upward,
              ),
            ),
          ],
        ),
        SizedBox(height: 12),
        
        // 6. Net Debts Card (Full Width)
        _buildGradientKPI(
          title: 'صافي الديون المستحقة',
          amount: netDebts,
          format: format,
          colors: [Color(0xFFF5F3FF), Color(0xFFEDE9FE)],
          borderColor: Color(0xFFDDD6FE),
          textColor: netDebts >= 0 ? Color(0xFF047857) : Color(0xFFBE123C),
          subtext: 'الفارق المالي بين ما لك وما عليك من ذمم',
          icon: Icons.balance,
          isSigned: true,
          isFullWidth: true,
        ),
      ],
    );
  }

  Widget _buildWealthCard(double amount, NumberFormat format) {
    return Container(
      height: 200,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF1E1B4B), Color(0xFF312E81), Color(0xFF4338CA)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(color: Color(0xFF4338CA).withOpacity(0.2), blurRadius: 20, offset: Offset(0, 10)),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
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
            Padding(
              padding: EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('صافي الثروة المقدرة', 
                        style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.7), fontWeight: FontWeight.bold, fontSize: 11, letterSpacing: 0.5)),
                      Container(
                        padding: EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(Icons.account_balance, color: Colors.amber.shade400, size: 16),
                      ),
                    ],
                  ),
                  SizedBox(height: 8),
                  Text(format.format(amount), 
                    style: GoogleFonts.almarai(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900, letterSpacing: -1.5)),
                  Spacer(),
                  Text('العملات الأخرى المتوفرة في حساباتك:', 
                    style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 9, fontWeight: FontWeight.bold)),
                  SizedBox(height: 8),
                  Row(
                    children: (data?['total_by_currency'] is Map ? (data?['total_by_currency'] as Map) : {}).entries.where((e) => e.key != 'USD').take(3).map((e) => Container(
                      margin: EdgeInsets.only(left: 8),
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1), 
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: Colors.white.withOpacity(0.05)),
                      ),
                      child: Text('${NumberFormat('#,##0').format(e.value)} ${e.key}', style: GoogleFonts.almarai(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w800)),
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

  Widget _buildGradientKPI({
    required String title,
    required double amount,
    required NumberFormat format,
    required List<Color> colors,
    required Color borderColor,
    required Color textColor,
    required String subtext,
    required IconData icon,
    bool isSigned = false,
    bool isFullWidth = false,
  }) {
    String signedAmount = format.format(amount.abs());
    if (isSigned) {
      signedAmount = (amount >= 0 ? '+' : '-') + signedAmount;
    }

    return Container(
      height: isFullWidth ? 95 : 110,
      padding: EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: colors,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: borderColor, width: 1.5),
        boxShadow: [
          BoxShadow(color: textColor.withOpacity(0.03), blurRadius: 10, offset: Offset(0, 5))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  title,
                  style: GoogleFonts.almarai(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    color: textColor.withOpacity(0.8),
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              Icon(icon, color: textColor, size: 14),
            ],
          ),
          SizedBox(height: 4),
          Text(
            signedAmount,
            style: GoogleFonts.almarai(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: textColor,
            ),
          ),
          SizedBox(height: 2),
          Text(
            subtext,
            style: GoogleFonts.almarai(
              fontSize: 8,
              fontWeight: FontWeight.bold,
              color: textColor.withOpacity(0.7),
            ),
            overflow: TextOverflow.ellipsis,
            maxLines: 1,
          ),
        ],
      ),
    );
  }

  Widget _buildUpcomingDebtsSection(NumberFormat format) {
    final List upcoming = data?['upcoming_debts'] as List? ?? [];
    if (upcoming.isEmpty) return SizedBox();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildSectionHeader('مواعيد الاستحقاق القريبة (التنبيهات)'),
        SizedBox(height: 15),
        ...upcoming.map((debt) {
          final int daysLeft = int.tryParse(debt['days_left']?.toString() ?? '0') ?? 0;
          final double remaining = double.tryParse(debt['remaining_amount']?.toString() ?? '0') ?? 0.0;
          final String partyName = debt['party_name'] ?? 'ذمة';
          final String type = debt['type'] ?? 'receivable';
          final String currency = debt['currency'] ?? 'USD';
          final String description = debt['description'] ?? 'بدون تفاصيل إضافية';
          final String typeLabel = debt['type_label'] ?? '';

          Color badgeBg;
          Color badgeText;
          String countdownText;

          if (daysLeft < 0) {
            badgeBg = Color(0xFFFEE2E2); // red-100
            badgeText = Color(0xFF991B1B); // red-800
            countdownText = 'متأخر منذ ${daysLeft.abs()} يوم';
          } else if (daysLeft == 0) {
            badgeBg = Color(0xFFFEF3C7); // amber-100
            badgeText = Color(0xFF92400E); // amber-800
            countdownText = 'اليوم هو موعد السداد';
          } else {
            badgeBg = Color(0xFFF1F5F9); // slate-100
            badgeText = Color(0xFF475569); // slate-600
            countdownText = 'متبقي $daysLeft يوم';
          }

          Color typeBg = type == 'receivable' ? Color(0xFFECFDF5) : Color(0xFFFFF1F2);
          Color typeText = type == 'receivable' ? Color(0xFF047857) : Color(0xFFBE123C);

          return Container(
            margin: EdgeInsets.only(bottom: 12),
            padding: EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
              boxShadow: [
                BoxShadow(
                  color: Colors.indigo.shade900.withOpacity(0.015),
                  blurRadius: 15,
                  offset: Offset(0, 6),
                )
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: typeBg,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        typeLabel,
                        style: GoogleFonts.almarai(
                          color: typeText,
                          fontWeight: FontWeight.bold,
                          fontSize: 9,
                        ),
                      ),
                    ),
                    Container(
                      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: badgeBg,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        countdownText,
                        style: GoogleFonts.almarai(
                          color: badgeText,
                          fontWeight: FontWeight.bold,
                          fontSize: 9,
                        ),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 10),
                Text(
                  partyName,
                  style: GoogleFonts.almarai(
                    fontWeight: FontWeight.w900,
                    fontSize: 14,
                    color: Color(0xFF0F172A),
                  ),
                ),
                SizedBox(height: 3),
                Text(
                  description,
                  style: GoogleFonts.almarai(
                    color: Color(0xFF94A3B8),
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                SizedBox(height: 12),
                Divider(color: Color(0xFFF8FAFC), height: 1, thickness: 1),
                SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'المبلغ المتبقي للسداد',
                          style: GoogleFonts.almarai(
                            color: Color(0xFF94A3B8),
                            fontSize: 8,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        SizedBox(height: 2),
                        Text(
                          '${NumberFormat('#,##0.00').format(remaining)} $currency',
                          style: GoogleFonts.almarai(
                            fontWeight: FontWeight.w900,
                            fontSize: 13,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                      ],
                    ),
                    Text(
                      'سداد الحساب ←',
                      style: GoogleFonts.almarai(
                        color: Colors.indigo.shade500,
                        fontWeight: FontWeight.w900,
                        fontSize: 11,
                      ),
                    ),
                  ],
                )
              ],
            ),
          );
        }).toList(),
      ],
    );
  }

  Widget _buildAssetSection(String title, {String? trailingValue, required Widget screen, required Widget list}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildSectionHeader(title, trailingValue: trailingValue, onTap: () => Get.to(() => screen)),
        SizedBox(height: 15),
        list,
      ],
    );
  }

  Widget _buildSectionHeader(String title, {String? trailingValue, VoidCallback? onTap}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Text(title, style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade900)),
            if (trailingValue != null) ...[
              SizedBox(width: 8),
              Container(
                padding: EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                decoration: BoxDecoration(
                  color: Colors.indigo.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.indigo.shade100.withOpacity(0.5)),
                ),
                child: Text(
                  trailingValue,
                  style: GoogleFonts.almarai(color: Colors.indigo.shade700, fontWeight: FontWeight.w900, fontSize: 11),
                ),
              ),
            ],
          ],
        ),
        if (onTap != null)
          TextButton(
            onPressed: onTap,
            child: Text('مشاهدة الكل', style: GoogleFonts.almarai(color: Colors.indigo.shade400, fontWeight: FontWeight.bold, fontSize: 13)),
          ),
      ],
    );
  }

  Widget _buildWalletsList() {
    final wallets = data?['wallets'] as List? ?? [];
    if (wallets.isEmpty) return _buildEmptyAsset('لا توجد محافظ');
    
    final double totalPersonalCash = double.tryParse(data?['estimated_personal_cash_usd']?.toString() ?? '0') ?? 0.0;

    return Container(
      height: 185,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: BouncingScrollPhysics(),
        itemCount: wallets.length,
        itemBuilder: (context, i) {
          final w = wallets[i];
          final double balance = double.tryParse(w['balance']?.toString() ?? '0') ?? 0.0;
          
          double balanceUSD = balance;
          final String currency = w['currency'] ?? 'USD';
          if (currency == 'SYP') {
            final double sypRate = double.tryParse(data?['syp_rate']?.toString() ?? '15000') ?? 15000.0;
            balanceUSD = sypRate > 0 ? balance / sypRate : balance;
          }
          final double percentageOfTotal = totalPersonalCash > 0 ? (balanceUSD / totalPersonalCash) * 100 : 0.0;
          final double progressPercent = (percentageOfTotal / 100).clamp(0.0, 1.0);

          return GestureDetector(
            onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id'])),
            child: Container(
              width: 220,
              margin: EdgeInsets.only(left: 15, bottom: 10),
              padding: EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFFF0F9FF), Color(0xFFE0F2FE)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(26), 
                border: Border.all(color: Color(0xFFBAE6FD), width: 1.5),
                boxShadow: [
                  BoxShadow(color: Color(0xFF0284C7).withOpacity(0.04), blurRadius: 15, offset: Offset(0, 8))
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.7),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Icon(Icons.account_balance_wallet_rounded, color: Color(0xFF0369A1), size: 18),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Color(0xFF0284C7),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          currency,
                          style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 9),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(w['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0C4A6E))),
                  SizedBox(height: 3),
                  Text('${NumberFormat('#,##0').format(balance)} $currency', style: GoogleFonts.almarai(color: Color(0xFF0369A1), fontWeight: FontWeight.w900, fontSize: 16)),
                  
                  // Visual Chart: Progress bar showing percentage of total liquidity cash!
                  SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'نسبة السيولة الشخصية',
                        style: GoogleFonts.almarai(fontSize: 8, color: Color(0xFF0369A1).withOpacity(0.7), fontWeight: FontWeight.bold),
                      ),
                      Text(
                        '${percentageOfTotal.toStringAsFixed(0)}%',
                        style: GoogleFonts.almarai(fontSize: 8, color: Color(0xFF0369A1), fontWeight: FontWeight.w900),
                      ),
                    ],
                  ),
                  SizedBox(height: 4),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: Container(
                      height: 4,
                      width: double.infinity,
                      color: Colors.white,
                      child: Align(
                        alignment: Alignment.centerRight,
                        child: Container(
                          width: 220 * progressPercent,
                          height: 4,
                          color: Color(0xFF0284C7),
                        ),
                      ),
                    ),
                  ),
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
    final businessList = data?['businesses'] as List? ?? [];
    
    if (fundsList.isEmpty && businessList.isEmpty) return _buildEmptyAsset('لا توجد استثمارات أو مشاريع');

    final List combined = [];
    for (var b in businessList) {
      combined.add({
        'is_business': true,
        ...b
      });
    }
    for (var f in fundsList) {
      combined.add({
        'is_business': false,
        ...f
      });
    }

    return Container(
      height: 190,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: BouncingScrollPhysics(),
        itemCount: combined.length,
        itemBuilder: (context, i) {
          final item = combined[i];
          final bool isBusiness = item['is_business'] == true;

          if (isBusiness) {
            final double totalValue = double.tryParse(item['total_value']?.toString() ?? '0') ?? 0.0;
            final String currency = item['currency'] ?? 'USD';
            return GestureDetector(
              onTap: () => Get.to(() => BusinessDetailScreen(businessId: item['id'])),
              child: Container(
                width: 220,
                margin: EdgeInsets.only(left: 15, bottom: 10),
                padding: EdgeInsets.all(22),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFFFEF3C7), Color(0xFFFDE68A).withOpacity(0.7)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(26), 
                  border: Border.all(color: Color(0xFFFCD34D), width: 1.5),
                  boxShadow: [
                    BoxShadow(color: Color(0xFFD97706).withOpacity(0.04), blurRadius: 15, offset: Offset(0, 8))
                  ],
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
                            color: Colors.white.withOpacity(0.7),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Icon(Icons.storefront, color: Color(0xFFB45309), size: 20),
                        ),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: Color(0xFFD97706),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            currency,
                            style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10),
                          ),
                        )
                      ],
                    ),
                    Spacer(),
                    Text(item['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFF78350F))),
                    SizedBox(height: 6),
                    Text('${NumberFormat('#,##0').format(totalValue)} $currency', style: GoogleFonts.almarai(color: Color(0xFFB45309), fontWeight: FontWeight.w900, fontSize: 18)),
                  ],
                ),
              ),
            );
          } else {
            final double currentValue = double.tryParse(item['current_value']?.toString() ?? '0') ?? 0.0;
            final double capital = double.tryParse(item['capital']?.toString() ?? '0') ?? 0.0;
            
            final double fundProfit = currentValue - capital;
            final double fundProfitPct = capital > 0 ? (fundProfit / capital) * 100 : 0.0;
            final double barPercent = capital > 0 ? (currentValue / capital).clamp(0.0, 1.0) : 0.0;

            final isProfit = fundProfit >= 0;
            final String currency = item['currency'] ?? 'USD';

            return GestureDetector(
              onTap: () => Get.to(() => FundDetailScreen(fundId: item['id'])),
              child: Container(
                width: 220,
                margin: EdgeInsets.only(left: 15, bottom: 10),
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFFFAF5FF), Color(0xFFF3E8FF)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(26), 
                  border: Border.all(color: Color(0xFFE9D5FF), width: 1.5),
                  boxShadow: [
                    BoxShadow(color: Color(0xFF7E22CE).withOpacity(0.04), blurRadius: 15, offset: Offset(0, 8))
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.7),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(item['icon'] ?? '📈', style: GoogleFonts.almarai(fontSize: 16)),
                        ),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: isProfit ? Color(0xFFD1FAE5) : Color(0xFFFEE2E2),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: isProfit ? Color(0xA010B981) : Color(0xA0EF4444), width: 0.5),
                          ),
                          child: Text(
                            '${isProfit ? '+' : ''}${fundProfitPct.toStringAsFixed(1)}%',
                            style: GoogleFonts.almarai(
                              color: isProfit ? Color(0xFF065F46) : Color(0xFF991B1B), 
                              fontWeight: FontWeight.w900, 
                              fontSize: 9
                            ),
                          ),
                        )
                      ],
                    ),
                    Spacer(),
                    Text(item['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF581C87))),
                    SizedBox(height: 3),
                    Text('${NumberFormat('#,##0').format(currentValue)} $currency', style: GoogleFonts.almarai(color: Color(0xFF7E22CE), fontWeight: FontWeight.w900, fontSize: 16)),
                    
                    // Visual Chart: Fund Investment Progress Bar (Value vs Capital)
                    SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'العائد الرأسمالي المقدر',
                          style: GoogleFonts.almarai(fontSize: 8, color: Color(0xFF7E22CE).withOpacity(0.7), fontWeight: FontWeight.bold),
                        ),
                        Text(
                          '${(barPercent * 100).toStringAsFixed(0)}%',
                          style: GoogleFonts.almarai(fontSize: 8, color: Color(0xFF7E22CE), fontWeight: FontWeight.w900),
                        ),
                      ],
                    ),
                    SizedBox(height: 4),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: Container(
                        height: 4,
                        width: double.infinity,
                        color: Colors.white,
                        child: Align(
                          alignment: Alignment.centerRight,
                          child: Container(
                            width: 220 * barPercent,
                            height: 4,
                            color: Color(0xFF7E22CE),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            );
          }
        },
      ),
    );
  }

  Widget _buildEmptyAsset(String text) {
    return Container(
      padding: EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(24), 
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
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
        Color typeColor = type == 'income' ? Colors.green.shade600 : (type == 'capital' ? Colors.indigo.shade600 : Colors.red.shade600);

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white, 
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))]
          ),
          child: Row(
            children: [
              Container(
                width: 48, height: 48,
                decoration: BoxDecoration(
                  color: iconColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(child: Text(categoryIcon, style: GoogleFonts.almarai(fontSize: 20))),
              ),
              SizedBox(width: 15),
              Expanded(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(description, style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFF0F172A))),
                  SizedBox(height: 3),
                  Text(categoryName, style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold)),
                ],
              )),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('${type == 'income' ? '+' : '-'}${NumberFormat('#,##0').format(double.parse(amount))}', 
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w900, 
                      fontSize: 16, 
                      color: typeColor,
                    )
                  ),
                  SizedBox(height: 3),
                  Text(currency, style: GoogleFonts.almarai(fontSize: 10, fontWeight: FontWeight.w800, color: Color(0xFF94A3B8))),
                ],
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildCashflowChart() {
    if (data == null) return SizedBox();
    
    final double personal = double.tryParse(data?['estimated_personal_cash_usd']?.toString() ?? '0') ?? 0.0;
    final double business = double.tryParse(data?['estimated_business_only_usd']?.toString() ?? '0') ?? 0.0;
    final double funds = double.tryParse(data?['estimated_funds_only_usd']?.toString() ?? '0') ?? 0.0;
    final double investmentsTotal = business + funds;
    final double total = personal + investmentsTotal;

    if (total == 0) {
      return _buildEmptyAsset('لا توجد بيانات سيولة متوفرة حالياً');
    }

    final double personalPct = total > 0 ? (personal / total) * 100 : 0;
    final double investmentsPct = total > 0 ? (investmentsTotal / total) * 100 : 0;

    return Container(
      height: 220,
      padding: EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(30),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.indigo.shade900.withOpacity(0.01),
            blurRadius: 20,
            offset: Offset(0, 8),
          )
        ],
      ),
      child: Row(
        children: [
          // Donut Chart
          Expanded(
            flex: 4,
            child: Container(
              height: 150,
              child: PieChart(
                PieChartData(
                  sectionsSpace: 4,
                  centerSpaceRadius: 42,
                  startDegreeOffset: 270,
                  sections: [
                    if (personal > 0)
                      PieChartSectionData(
                        color: Color(0xFF0284C7),
                        value: personal,
                        title: '${personalPct.toStringAsFixed(0)}%',
                        radius: 20,
                        titleStyle: GoogleFonts.almarai(
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                        ),
                      ),
                    if (investmentsTotal > 0)
                      PieChartSectionData(
                        color: Color(0xFF7E22CE),
                        value: investmentsTotal,
                        title: '${investmentsPct.toStringAsFixed(0)}%',
                        radius: 20,
                        titleStyle: GoogleFonts.almarai(
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ),
          SizedBox(width: 15),
          // Legend
          Expanded(
            flex: 5,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildLegendItem('محافظ شخصية', personal, personalPct, Color(0xFF0284C7)),
                SizedBox(height: 15),
                _buildLegendItem('الاستثمارات والمشاريع', investmentsTotal, investmentsPct, Color(0xFF7E22CE)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegendItem(String title, double amount, double percentage, Color color) {
    return Row(
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(4),
          ),
        ),
        SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w800,
                      fontSize: 11,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  Text(
                    '${percentage.toStringAsFixed(1)}%',
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w900,
                      fontSize: 10,
                      color: color,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 2),
              Text(
                '\$${NumberFormat('#,##0').format(amount)}',
                style: GoogleFonts.almarai(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF64748B),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildPremiumFAB() {
    return GestureDetector(
      onTap: () async {
        final refresh = await Get.to(() => AddTransactionScreen());
        if (refresh == true) loadData();
      },
      child: Container(
        width: 190,
        height: 56,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF4338CA), Color(0xFF6366F1)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(28),
          boxShadow: [BoxShadow(color: Color(0xFF6366F1).withOpacity(0.35), blurRadius: 20, offset: Offset(0, 10))],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.add, color: Colors.white, size: 22),
            SizedBox(width: 8),
            Text('تسجيل حركة جديدة', style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15)),
          ],
        ),
      ),
    );
  }
}
