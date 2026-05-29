import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';
import 'add_transaction_screen.dart';

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

  void _confirmDeleteWallet() {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف المحفظة الشخصية', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.red.shade900)),
        content: Text('هل أنت متأكد من حذف هذه المحفظة نهائياً؟ سيؤدي ذلك لحذف كافة العمليات والعهود المرتبطة بها ولا يمكن الاستعادة.', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 13)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف المحفظة', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              final success = await apiService.deleteWallet(widget.walletId);
              if (success) {
                Get.back(result: true);
                Get.snackbar('تم الحذف', 'تم حذف المحفظة بنجاح');
              }
            },
          ),
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    final format = NumberFormat('#,##0', 'en_US');
    final w = data;
    final walletName = w?['name'] ?? 'تفاصيل المحفظة';

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          walletName,
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios, color: Color(0xFF0F172A), size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.delete_outline_rounded, color: Colors.red.shade700, size: 20),
            onPressed: () => _confirmDeleteWallet(),
          ),
          SizedBox(width: 10),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Colors.indigo))
          : RefreshIndicator(
              onRefresh: () async => loadDetail(),
              color: Colors.indigo,
              child: SingleChildScrollView(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // 1. Premium Dark Slate Header Hero Card
                    _buildHeaderHeroCard(w, format),
                    SizedBox(height: 25),

                    // 2. Action Buttons (Add Operation)
                    _buildQuickActions(),
                    SizedBox(height: 30),

                    // 3. Rich 3-Card Stats Row
                    _buildSectionHeader('الخلاصة الإحصائية للمحفظة'),
                    SizedBox(height: 15),
                    _buildStatsRow(w, format),
                    SizedBox(height: 35),

                    // 4. Sub-Accounts & Custodians (الحسابات والعهود الفرعية)
                    _buildSectionHeader('الحسابات والعهود الفرعية'),
                    SizedBox(height: 15),
                    _buildSubAccountsSection(w, format),
                    SizedBox(height: 35),

                    // 5. Recent Transactions Section
                    _buildSectionHeader('الحركات والعمليات الأخيرة'),
                    SizedBox(height: 15),
                    _buildTransactionsSection(w, format),
                    SizedBox(height: 50),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Text(
      title,
      style: GoogleFonts.almarai(
        fontSize: 16,
        fontWeight: FontWeight.w900,
        color: Color(0xFF0F172A),
      ),
    );
  }

  Widget _buildHeaderHeroCard(dynamic w, NumberFormat format) {
    final String currency = w?['currency'] ?? 'USD';
    final double balance = double.tryParse(w?['balance']?.toString() ?? '0') ?? 0.0;
    final double sypRate = double.tryParse(w?['syp_rate']?.toString() ?? '0') ?? 0.0;
    final String custodianName = w?['custodian_name'] ?? '';

    final bool isSYP = currency == 'SYP' && sypRate > 0;
    final double usdEquivalent = isSYP ? balance / sypRate : balance;

    return Container(
      padding: EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E293B), Color(0xFF334155)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            color: Color(0xFF0F172A).withOpacity(0.15),
            blurRadius: 20,
            offset: Offset(0, 10),
          )
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(30),
        child: Stack(
          children: [
            Positioned(
              right: -30,
              bottom: -30,
              child: CircleAvatar(
                radius: 80,
                backgroundColor: Colors.white.withOpacity(0.02),
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'الرصيد المتاح الحالي',
                      style: GoogleFonts.almarai(
                        color: Colors.white.withOpacity(0.6),
                        fontWeight: FontWeight.bold,
                        fontSize: 11,
                        letterSpacing: 0.5,
                      ),
                    ),
                    if (custodianName.isNotEmpty)
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Color(0xFFD97706).withOpacity(0.2),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: Color(0xFFF59E0B).withOpacity(0.3)),
                        ),
                        child: Text(
                          'عهدة: $custodianName',
                          style: GoogleFonts.almarai(
                            color: Color(0xFFFBBF24),
                            fontWeight: FontWeight.bold,
                            fontSize: 9,
                          ),
                        ),
                      ),
                  ],
                ),
                SizedBox(height: 10),
                Text(
                  '${format.format(balance)} $currency',
                  style: GoogleFonts.almarai(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -1.0,
                  ),
                ),
                if (isSYP) ...[
                  SizedBox(height: 15),
                  Row(
                    children: [
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.07),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.white.withOpacity(0.1)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'ما يعادل بالدولار',
                              style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.4), fontSize: 8, fontWeight: FontWeight.bold),
                            ),
                            SizedBox(height: 2),
                            Text(
                              '\$${NumberFormat('#,##0.00').format(usdEquivalent)}',
                              style: GoogleFonts.almarai(color: Color(0xFF34D399), fontSize: 14, fontWeight: FontWeight.w900),
                            ),
                          ],
                        ),
                      ),
                      SizedBox(width: 10),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.07),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.white.withOpacity(0.1)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'سعر الصرف',
                              style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.4), fontSize: 8, fontWeight: FontWeight.bold),
                            ),
                            SizedBox(height: 2),
                            Text(
                              '${format.format(sypRate)} ل.س',
                              style: GoogleFonts.almarai(color: Color(0xFFFBBF24), fontSize: 13, fontWeight: FontWeight.w900),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
                SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildHeroMiniStat('حالة المحفظة', 'نشطة ومتوفرة'),
                    _buildHeroMiniStat('العملة الأساسية', currency),
                    _buildHeroMiniStat('النوع', custodianName.isNotEmpty ? 'عهدة شخصية' : 'محفظة أساسية'),
                  ],
                )
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeroMiniStat(String label, String val) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.4), fontSize: 9, fontWeight: FontWeight.bold),
        ),
        SizedBox(height: 3),
        Text(
          val,
          style: GoogleFonts.almarai(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w800),
        ),
      ],
    );
  }

  Widget _buildQuickActions() {
    return GestureDetector(
      onTap: () async {
        final res = await Get.to(() => AddTransactionScreen(), arguments: {
          'accountType': 'wallet',
          'accountId': widget.walletId,
        });
        if (res == true) loadDetail();
      },
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(
          color: Color(0xFF0F172A).withOpacity(0.08),
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: Color(0xFF0F172A).withOpacity(0.15), width: 1.5),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.add_circle_outline_rounded, color: Color(0xFF0F172A), size: 20),
            SizedBox(width: 8),
            Text(
              'تسجيل حركة جديدة بالمحفظة',
              style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontWeight: FontWeight.w900, fontSize: 13),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsRow(dynamic w, NumberFormat format) {
    final String currency = w?['currency'] ?? 'USD';
    final double totalIncome = double.tryParse(w?['total_income']?.toString() ?? '0') ?? 0.0;
    final double totalExpense = double.tryParse(w?['total_expense']?.toString() ?? '0') ?? 0.0;
    final int count = int.tryParse(w?['transactions_count']?.toString() ?? '0') ?? 0;

    return Row(
      children: [
        Expanded(
          child: _buildStatCard('إجمالي الإيداعات', totalIncome, currency, format, Color(0xFF059669)),
        ),
        SizedBox(width: 10),
        Expanded(
          child: _buildStatCard('إجمالي السحوبات', totalExpense, currency, format, Color(0xFFDC2626)),
        ),
        SizedBox(width: 10),
        Expanded(
          child: Container(
            height: 90,
            padding: EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'عدد العمليات',
                  style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 9),
                ),
                Text(
                  '$count حركة',
                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 15),
                ),
                Text(
                  'المسجلة بالكامل',
                  style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 8),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStatCard(String label, double value, String currency, NumberFormat format, Color color) {
    return Container(
      height: 90,
      padding: EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 9),
            overflow: TextOverflow.ellipsis,
          ),
          Text(
            format.format(value),
            style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: color, fontSize: 15),
            overflow: TextOverflow.ellipsis,
          ),
          Text(
            currency,
            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 8),
          ),
        ],
      ),
    );
  }

  Widget _buildSubAccountsSection(dynamic w, NumberFormat format) {
    final subAccounts = w?['payment_methods'] as List? ?? [];
    if (subAccounts.isEmpty) {
      return _buildEmptyState('لا توجد حسابات أو عهد فرعية مسجلة');
    }

    final bankAccounts = subAccounts.where((sa) => sa['type'] == 'bank' && sa['parent_id'] == null).toList();
    final otherAccounts = subAccounts.where((sa) => sa['parent_id'] == null && sa['type'] != 'bank' && (sa['custodian_name'] == null || sa['custodian_name'].toString().isEmpty)).toList();
    final custodies = subAccounts.where((sa) => sa['custodian_name'] != null && sa['custodian_name'].toString().isNotEmpty).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (bankAccounts.isNotEmpty) ...[
          Text(
            '🏛️ الحسابات البنكية والبطاقات المرتبطة',
            style: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
          ),
          SizedBox(height: 10),
          ...bankAccounts.map((bank) {
            final double balance = double.tryParse(bank['balance']?.toString() ?? '0') ?? 0.0;
            final String currency = bank['currency'] ?? 'USD';
            final int bankId = bank['id'];
            final linkedCards = subAccounts.where((sa) => sa['parent_id'] == bankId).toList();

            return Container(
              margin: EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Padding(
                    padding: EdgeInsets.all(16),
                    child: Row(
                      children: [
                        Container(
                          padding: EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: Colors.indigo.shade50,
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Icon(Icons.account_balance_rounded, color: Colors.indigo, size: 20),
                        ),
                        SizedBox(width: 15),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                bank['name'] ?? 'حساب بنكي رئيسي',
                                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0F172A)),
                              ),
                              Text(
                                'حساب بنكي رئيسي',
                                style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                              )
                            ],
                          ),
                        ),
                        Text(
                          '${format.format(balance)} $currency',
                          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 14),
                        ),
                      ],
                    ),
                  ),
                  if (linkedCards.isNotEmpty) ...[
                    Container(
                      color: Color(0xFFF8FAFC),
                      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            '💳 البطاقات المرتبطة بالحساب:',
                            style: GoogleFonts.almarai(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.indigo),
                          ),
                          SizedBox(height: 8),
                          ...linkedCards.map((card) {
                            final double cardBalance = double.tryParse(card['balance']?.toString() ?? '0') ?? 0.0;
                            final String cardCurrency = card['currency'] ?? 'USD';
                            final String cardType = card['type'] ?? 'credit_card';

                            return Container(
                              margin: EdgeInsets.only(bottom: 8),
                              padding: EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: Color(0xFFE2E8F0)),
                              ),
                              child: Row(
                                children: [
                                  Icon(Icons.credit_card_rounded, color: Colors.indigo.shade400, size: 16),
                                  SizedBox(width: 10),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          card['name'] ?? 'بطاقة بنكية',
                                          style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF334155)),
                                        ),
                                        Text(
                                          cardType == 'credit_card' ? 'بطاقة ائتمانية' : 'بطاقة دفع',
                                          style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 9, fontWeight: FontWeight.bold),
                                        )
                                      ],
                                    ),
                                  ),
                                  Text(
                                    '${format.format(cardBalance)} $cardCurrency',
                                    style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF475569), fontSize: 12),
                                  ),
                                ],
                              ),
                            );
                          }).toList(),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            );
          }).toList(),
          SizedBox(height: 20),
        ],
        if (otherAccounts.isNotEmpty) ...[
          Text(
            '💳 الحسابات والبطاقات المستقلة',
            style: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
          ),
          SizedBox(height: 10),
          ...otherAccounts.map((sa) {
            final double balance = double.tryParse(sa['balance']?.toString() ?? '0') ?? 0.0;
            final String currency = sa['currency'] ?? 'USD';
            final String type = sa['type'] ?? 'cash';

            return Container(
              margin: EdgeInsets.only(bottom: 12),
              padding: EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
              ),
              child: Row(
                children: [
                  Container(
                    padding: EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      type == 'cash' ? Icons.money_rounded : Icons.credit_card_rounded,
                      color: Color(0xFF475569),
                      size: 20,
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          sa['name'] ?? 'حساب فرعي',
                          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0F172A)),
                        ),
                        Text(
                          type == 'cash' ? 'نقد / كاش' : 'بطاقة مستقلة',
                          style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                        )
                      ],
                    ),
                  ),
                  Text(
                    '${format.format(balance)} $currency',
                    style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 14),
                  ),
                ],
              ),
            );
          }).toList(),
          SizedBox(height: 20),
        ],
        if (custodies.isNotEmpty) ...[
          Text(
            '💼 العهد المالية',
            style: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF64748B)),
          ),
          SizedBox(height: 10),
          ...custodies.map((sa) {
            final double balance = double.tryParse(sa['balance']?.toString() ?? '0') ?? 0.0;
            final String currency = sa['currency'] ?? 'USD';
            final String custodianName = sa['custodian_name'] ?? '';

            return Container(
              margin: EdgeInsets.only(bottom: 12),
              padding: EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Color(0xFFFEF3C7).withOpacity(0.2),
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: Color(0xFFFDE68A), width: 1.5),
              ),
              child: Row(
                children: [
                  Container(
                    padding: EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Color(0xFFF59E0B).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      Icons.folder_shared_rounded,
                      color: Color(0xFFD97706),
                      size: 20,
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          sa['name'] ?? 'عهدة فرعية',
                          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF92400E)),
                        ),
                        Text(
                          'بعهدة المسؤول المالي: $custodianName',
                          style: GoogleFonts.almarai(color: Color(0xFFB45309), fontSize: 10, fontWeight: FontWeight.bold),
                        )
                      ],
                    ),
                  ),
                  Text(
                    '${format.format(balance)} $currency',
                    style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF92400E), fontSize: 14),
                  ),
                ],
              ),
            );
          }).toList(),
        ],
      ],
    );
  }

  Widget _buildTransactionsSection(dynamic w, NumberFormat format) {
    final txs = w?['transactions'] as List? ?? [];
    if (txs.isEmpty) {
      return _buildEmptyState('لا توجد حركات مسجلة حالياً لهذه المحفظة');
    }

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
        final double amount = double.tryParse(t['amount']?.toString() ?? '0') ?? 0.0;
        final String type = t['type'] ?? 'expense';

        final String currency = (t['payment_method'] != null && t['payment_method']['currency'] != null)
            ? t['payment_method']['currency'].toString()
            : (w?['currency'] ?? 'USD');

        Color iconColor = Color(int.parse(categoryColor.replaceFirst('#', '0xFF')));
        Color typeColor = type == 'income'
            ? Colors.green.shade600
            : (type == 'capital' ? Colors.indigo.shade600 : Colors.red.shade600);

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: iconColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(child: Text(categoryIcon, style: TextStyle(fontSize: 18))),
              ),
              SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      description,
                      style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0F172A)),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    SizedBox(height: 3),
                    Row(
                      children: [
                        Text(
                          categoryName,
                          style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                        ),
                        if (t['payment_method'] != null) ...[
                          SizedBox(width: 8),
                          Container(
                            width: 3,
                            height: 3,
                            decoration: BoxDecoration(color: Color(0xFFCBD5E1), shape: BoxShape.circle),
                          ),
                          SizedBox(width: 8),
                          Text(
                            t['payment_method']['name'] ?? '',
                            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                          ),
                        ]
                      ],
                    ),
                  ],
                ),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    '${type == 'income' ? '+' : '-'}${format.format(amount)}',
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                      color: typeColor,
                    ),
                  ),
                  SizedBox(height: 2),
                  Text(
                    currency,
                    style: GoogleFonts.almarai(fontSize: 9, fontWeight: FontWeight.w800, color: Color(0xFF94A3B8)),
                  ),
                ],
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildEmptyState(String text) {
    return Container(
      padding: EdgeInsets.symmetric(vertical: 30, horizontal: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
      ),
      child: Center(
        child: Text(
          text,
          style: GoogleFonts.almarai(
            color: Color(0xFF94A3B8),
            fontWeight: FontWeight.bold,
            fontSize: 11,
          ),
          textAlign: TextAlign.center,
        ),
      ),
    );
  }
}
