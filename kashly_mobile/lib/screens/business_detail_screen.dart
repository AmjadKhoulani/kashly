import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';
import 'add_transaction_screen.dart';

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

  void _confirmDeleteBusiness() {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف النشاط التجاري', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.red.shade900)),
        content: Text('هل أنت متأكد من حذف هذا النشاط التجاري نهائياً؟ سيؤدي ذلك لحذف كافة العمليات والبيانات المرتبطة به ولا يمكن التراجع عن ذلك.', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 13)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف النشاط', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              final success = await apiService.deleteBusiness(widget.businessId);
              if (success) {
                Get.back(result: true);
                Get.snackbar('تم الحذف', 'تم حذف النشاط التجاري بنجاح');
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
    final b = data;
    final businessName = b?['name'] ?? 'تفاصيل النشاط';

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          businessName,
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
            onPressed: () => _confirmDeleteBusiness(),
          ),
          SizedBox(width: 10),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Colors.amber.shade700))
          : RefreshIndicator(
              onRefresh: () async => loadDetail(),
              color: Colors.amber.shade700,
              child: SingleChildScrollView(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // 1. Premium Amber Header Hero Card
                    _buildHeaderHeroCard(b, format),
                    SizedBox(height: 25),

                    // 2. Action Buttons (Add Operation)
                    _buildQuickActions(),
                    SizedBox(height: 30),

                    // 3. Rich 3-Card Stats Row
                    _buildSectionHeader('الخلاصة الإحصائية للنشاط التجاري'),
                    SizedBox(height: 15),
                    _buildStatsRow(b, format),
                    SizedBox(height: 35),

                    // 4. Recent Transactions Section
                    _buildSectionHeader('الحركات والعمليات الأخيرة'),
                    SizedBox(height: 15),
                    _buildTransactionsSection(b, format),
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

  Widget _buildHeaderHeroCard(dynamic b, NumberFormat format) {
    final String currency = b?['currency'] ?? 'USD';
    final double totalValue = double.tryParse(b?['total_value']?.toString() ?? '0') ?? 0.0;

    return Container(
      padding: EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF78350F), Color(0xFFB45309), Color(0xFFD97706)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            color: Color(0xFFB45309).withOpacity(0.15),
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
                      'إجمالي قيمة النشاط التجاري المقدرة',
                      style: GoogleFonts.almarai(
                        color: Colors.white.withOpacity(0.7),
                        fontWeight: FontWeight.bold,
                        fontSize: 11,
                        letterSpacing: 0.5,
                      ),
                    ),
                    Container(
                      padding: EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.storefront_rounded, color: Color(0xFFFDE68A), size: 16),
                    ),
                  ],
                ),
                SizedBox(height: 10),
                Text(
                  '${format.format(totalValue)} $currency',
                  style: GoogleFonts.almarai(
                    color: Colors.white,
                    fontSize: 32,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -1.0,
                  ),
                ),
                SizedBox(height: 25),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildHeroMiniStat('حالة القطاع', 'نشط وتجاري'),
                    _buildHeroMiniStat('العملة الأساسية', currency),
                    _buildHeroMiniStat('التصنيف الرئيسي', 'مشاريع وأعمال'),
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
          'accountType': 'business',
          'accountId': widget.businessId,
        });
        if (res == true) loadDetail();
      },
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(
          color: Color(0xFFB45309).withOpacity(0.08),
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: Color(0xFFB45309).withOpacity(0.15), width: 1.5),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.add_circle_outline_rounded, color: Color(0xFFB45309), size: 20),
            SizedBox(width: 8),
            Text(
              'تسجيل حركة جديدة للنشاط',
              style: GoogleFonts.almarai(color: Color(0xFFB45309), fontWeight: FontWeight.w900, fontSize: 13),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsRow(dynamic b, NumberFormat format) {
    final String currency = b?['currency'] ?? 'USD';
    final double totalIncome = double.tryParse(b?['total_income']?.toString() ?? '0') ?? 0.0;
    final double totalExpense = double.tryParse(b?['total_expense']?.toString() ?? '0') ?? 0.0;
    final int count = int.tryParse(b?['transactions_count']?.toString() ?? '0') ?? 0;

    return Row(
      children: [
        Expanded(
          child: _buildStatCard('إجمالي الإيرادات', totalIncome, currency, format, Color(0xFF059669)),
        ),
        SizedBox(width: 10),
        Expanded(
          child: _buildStatCard('إجمالي التكاليف', totalExpense, currency, format, Color(0xFFDC2626)),
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
                  'عدد الحركات',
                  style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 9),
                ),
                Text(
                  '$count حركة',
                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 15),
                ),
                Text(
                  'المسجلة كلياً',
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

  Widget _buildTransactionsSection(dynamic b, NumberFormat format) {
    final txs = b?['transactions'] as List? ?? [];
    if (txs.isEmpty) {
      return _buildEmptyState('لا توجد حركات مسجلة حالياً لهذا النشاط');
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
            : (b?['currency'] ?? 'USD');

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
