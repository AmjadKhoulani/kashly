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
      backgroundColor: Color(0xFFF8FAFC),
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
                        _buildMainWealthCard(currencyFormat),
                        SizedBox(height: 30),
                        
                        // New Ledger (Debts/Claims) Section
                        _buildLedgerSection(currencyFormat),
                        SizedBox(height: 30),
                        
                        // New Upcoming Payment Reminders Section
                        _buildUpcomingDebtsSection(currencyFormat),
                        SizedBox(height: 30),
                        
                        _buildSectionHeader('نظرة عامة على السيولة'),
                        SizedBox(height: 15),
                        _buildCashflowChart(),
                        SizedBox(height: 35),
                        
                        _buildAssetSection(
                          'المحافظ الشخصية', 
                          trailingValue: '\$${NumberFormat('#,##0').format(data?['estimated_personal_cash_usd'] ?? 0)}',
                          screen: WalletsScreen(), 
                          list: _buildWalletsList()
                        ),
                        SizedBox(height: 35),
                        
                        _buildAssetSection(
                          'قطاع الأعمال', 
                          trailingValue: '\$${NumberFormat('#,##0').format(data?['estimated_business_only_usd'] ?? 0)}',
                          screen: BusinessesScreen(), 
                          list: _buildBusinessesList()
                        ),
                        SizedBox(height: 35),
                        
                        _buildAssetSection(
                          'الاستثمارات', 
                          trailingValue: '\$${NumberFormat('#,##0').format(data?['estimated_funds_only_usd'] ?? 0)}',
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
      floatingActionButton: _buildPremiumFAB(),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 100,
      floating: true,
      pinned: true,
      backgroundColor: Color(0xFFF8FAFC),
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
              border: Border.all(color: Color(0xFFF1F5F9)),
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

  Widget _buildMainWealthCard(NumberFormat format) {
    return Container(
      height: 220,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF1E1B4B), Color(0xFF312E81), Color(0xFF4338CA)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(color: Color(0xFF4338CA).withOpacity(0.2), blurRadius: 20, offset: Offset(0, 10)),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(28),
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
              padding: EdgeInsets.all(26),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('صافي الثروة المقدرة', 
                        style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.7), fontWeight: FontWeight.bold, fontSize: 12, letterSpacing: 0.5)),
                      Container(
                        padding: EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(Icons.account_balance, color: Colors.amber.shade400, size: 16),
                      ),
                    ],
                  ),
                  SizedBox(height: 10),
                  Text(format.format(data?['estimated_total_usd'] ?? 0), 
                    style: GoogleFonts.almarai(color: Colors.white, fontSize: 40, fontWeight: FontWeight.w900, letterSpacing: -1.5)),
                  Spacer(),
                  Text('العملات الأخرى المتوفرة:', 
                    style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 10, fontWeight: FontWeight.bold)),
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
                      child: Text('${NumberFormat('#,##0').format(e.value)} ${e.key}', style: GoogleFonts.almarai(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w800)),
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

  Widget _buildLedgerSection(NumberFormat format) {
    final double receivables = (data?['total_receivables_usd'] ?? 0).toDouble();
    final double payables = (data?['total_payables_usd'] ?? 0).toDouble();
    final double netDebts = (data?['net_debts_usd'] ?? 0).toDouble();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildSectionHeader('الديون والالتزامات (الدفتر)'),
        SizedBox(height: 15),
        Container(
          height: 105,
          child: ListView(
            scrollDirection: Axis.horizontal,
            physics: BouncingScrollPhysics(),
            children: [
              // Receivables (ديون لي)
              _buildLedgerCard(
                title: 'ديون لي (مستحقات)',
                amount: receivables,
                bgColor: Color(0xFFECFDF5),
                textColor: Color(0xFF047857),
                icon: Icons.arrow_downward,
                format: format,
              ),
              // Payables (ديون علي)
              _buildLedgerCard(
                title: 'ديون عليّ (التزامات)',
                amount: payables,
                bgColor: Color(0xFFFFF1F2),
                textColor: Color(0xFFBE123C),
                icon: Icons.arrow_upward,
                format: format,
              ),
              // Net Debt (صافي الديون)
              _buildLedgerCard(
                title: 'صافي الديون المستحقة',
                amount: netDebts,
                bgColor: Color(0xFFF5F3FF),
                textColor: netDebts >= 0 ? Color(0xFF047857) : Color(0xFFBE123C),
                icon: Icons.balance,
                format: format,
                isSigned: true,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLedgerCard({
    required String title,
    required double amount,
    required Color bgColor,
    required Color textColor,
    required IconData icon,
    required NumberFormat format,
    bool isSigned = false,
  }) {
    String signedAmount = format.format(amount.abs());
    if (isSigned) {
      signedAmount = (amount >= 0 ? '+' : '-') + signedAmount;
    }

    return Container(
      width: 190,
      margin: EdgeInsets.only(left: 12, bottom: 5),
      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: textColor.withOpacity(0.1), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: textColor.withOpacity(0.015),
            blurRadius: 10,
            offset: Offset(0, 4),
          )
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
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                signedAmount,
                style: GoogleFonts.almarai(
                  fontSize: 16,
                  fontWeight: FontWeight.w900,
                  color: textColor,
                ),
              ),
              Text(
                'مبالغ مقدرة بالدولار',
                style: GoogleFonts.almarai(
                  fontSize: 8,
                  fontWeight: FontWeight.bold,
                  color: textColor.withOpacity(0.6),
                ),
              ),
            ],
          )
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
          final int daysLeft = (debt['days_left'] ?? 0).toInt();
          final double remaining = (debt['remaining_amount'] ?? 0).toDouble();
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
              border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
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
    return Container(
      height: 170,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: BouncingScrollPhysics(),
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
                color: Colors.white,
                borderRadius: BorderRadius.circular(24), 
                border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
                boxShadow: [BoxShadow(color: Colors.indigo.shade900.withOpacity(0.015), blurRadius: 20, offset: Offset(0, 10))],
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
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Icon(Icons.account_balance_wallet, color: Colors.indigo.shade600, size: 20),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          w['currency'] ?? 'USD',
                          style: GoogleFonts.almarai(color: Colors.green.shade700, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(w['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.blueGrey.shade900)),
                  SizedBox(height: 6),
                  Text('${NumberFormat('#,##0').format(w['balance'])} ${w['currency']}', style: GoogleFonts.almarai(color: Colors.indigo.shade700, fontWeight: FontWeight.w900, fontSize: 18)),
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
        physics: BouncingScrollPhysics(),
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
                color: Colors.white,
                borderRadius: BorderRadius.circular(24), 
                border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
                boxShadow: [BoxShadow(color: Colors.amber.shade900.withOpacity(0.015), blurRadius: 20, offset: Offset(0, 10))],
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
                          color: Colors.amber.shade50,
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Icon(Icons.storefront, color: Colors.amber.shade800, size: 20),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.amber.shade800,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          b['currency'] ?? 'USD',
                          style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(b['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.amber.shade900)),
                  SizedBox(height: 6),
                  Text('${NumberFormat('#,##0').format(b['total_value'])} ${b['currency'] ?? 'USD'}', style: GoogleFonts.almarai(color: Colors.amber.shade900, fontWeight: FontWeight.w900, fontSize: 18)),
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
        physics: BouncingScrollPhysics(),
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
                color: Colors.white,
                borderRadius: BorderRadius.circular(24), 
                border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
                boxShadow: [BoxShadow(color: Colors.green.shade900.withOpacity(0.015), blurRadius: 20, offset: Offset(0, 10))],
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
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Text(f['icon'] ?? '📈', style: GoogleFonts.almarai(fontSize: 18)),
                      ),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade700,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          f['currency'] ?? 'USD',
                          style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10),
                        ),
                      )
                    ],
                  ),
                  Spacer(),
                  Text(f['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Colors.green.shade900)),
                  SizedBox(height: 6),
                  Text('${NumberFormat('#,##0').format(f['current_value'])} ${f['currency']}', style: GoogleFonts.almarai(color: Colors.green.shade800, fontWeight: FontWeight.w900, fontSize: 18)),
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
      padding: EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(24), 
        border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
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
            border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
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
        borderRadius: BorderRadius.circular(30),
        border: Border.all(color: Color(0xFFF1F5F9), width: 1.5),
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
                  if (value == 0) return Text('0', style: GoogleFonts.almarai(color: Colors.indigo.shade100, fontSize: 10));
                  return Text('${(value / 1000).toStringAsFixed(0)}k', style: GoogleFonts.almarai(color: Colors.indigo.shade200, fontWeight: FontWeight.bold, fontSize: 10));
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
