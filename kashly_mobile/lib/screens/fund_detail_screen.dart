import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
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

  void _confirmDeleteFund() {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف الكيان الاستثماري', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.red.shade900)),
        content: Text('هل أنت متأكد من حذف هذا الكيان بالكامل؟ سيؤدي ذلك لحذف كافة الحسابات والعمليات والأصول المرتبطة به ولا يمكن التراجع عن ذلك.', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 13)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف الكيان', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              final success = await apiService.deleteFund(widget.fundId);
              if (success) {
                Get.back(result: true);
                Get.snackbar('تم الحذف', 'تم حذف الكيان الاستثماري بنجاح');
              }
            },
          ),
        ],
      )
    );
  }

  void _showFundDialog({Map? fund}) {
    final nameController = TextEditingController(text: fund?['name']);
    final capitalController = TextEditingController(text: fund?['capital']?.toString() ?? '0');
    final valController = TextEditingController(text: fund?['current_value']?.toString() ?? '0');
    final iconController = TextEditingController(text: fund?['icon'] ?? '🏘️');
    final distController = TextEditingController(text: fund?['distribution_frequency'] ?? 'شهري');
    String currency = fund?['currency'] ?? 'USD';
    String status = fund?['status'] ?? 'active';

    Get.bottomSheet(
      Container(
        padding: EdgeInsets.all(25),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(35)),
        ),
        child: SingleChildScrollView(
          physics: BouncingScrollPhysics(),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Text(
                  'تعديل الكيان الاستثماري',
                  style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.indigo.shade900),
                ),
              ),
              SizedBox(height: 25),
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: 'اسم الكيان / العقار',
                  labelStyle: GoogleFonts.almarai(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: iconController,
                      decoration: InputDecoration(
                        labelText: 'رمز تعبيري (Icon)',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: TextField(
                      controller: distController,
                      decoration: InputDecoration(
                        labelText: 'دورية التوزيع',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: capitalController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'رأس مال الكيان',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: TextField(
                      controller: valController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'القيمة السوقية',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: currency,
                      decoration: InputDecoration(
                        labelText: 'العملة',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      items: ['USD', 'SYP', 'AED', 'SAR'].map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                      onChanged: (val) => currency = val!,
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: status,
                      decoration: InputDecoration(
                        labelText: 'حالة الصندوق',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      items: [
                        DropdownMenuItem(value: 'active', child: Text('نشط / مستمر', style: GoogleFonts.almarai(fontSize: 12))),
                        DropdownMenuItem(value: 'completed', child: Text('مكتمل / مغلق', style: GoogleFonts.almarai(fontSize: 12))),
                      ],
                      onChanged: (val) => status = val!,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 30),
              ElevatedButton(
                onPressed: () async {
                  final data = {
                    'name': nameController.text,
                    'capital': capitalController.text,
                    'current_value': valController.text,
                    'currency': currency,
                    'distribution_frequency': distController.text,
                    'icon': iconController.text,
                    'status': status,
                  };
                  final success = await apiService.updateFund(widget.fundId, data);
                  if (success) {
                    Get.back();
                    loadDetail();
                    Get.snackbar('تم بنجاح', 'تم تحديث الكيان الاستثماري بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.indigo,
                  minimumSize: Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                child: Text(
                  'حفظ الكيان الاستثماري',
                  style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final format = NumberFormat('#,##0', 'en_US');
    final fund = data?['fund'];
    final fundName = fund?['name'] ?? 'تفاصيل الكيان';

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          fundName,
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
            icon: Icon(Icons.edit_outlined, color: Color(0xFF0F172A), size: 20),
            onPressed: () => _showFundDialog(fund: fund),
          ),
          IconButton(
            icon: Icon(Icons.delete_outline_rounded, color: Colors.red.shade700, size: 20),
            onPressed: () => _confirmDeleteFund(),
          ),
          SizedBox(width: 8),
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
                    // 1. Premium Indigo Glassmorphic Header Card
                    _buildHeaderHeroCard(fund, format),
                    SizedBox(height: 25),

                    // 2. Action Buttons Row
                    _buildQuickActions(fund),
                    SizedBox(height: 30),

                    // 3. Redesigned 5-Card Stats Grid (from web dashboard)
                    _buildSectionHeader('الخلاصة الإحصائية للكيان'),
                    SizedBox(height: 15),
                    _buildStatsGrid(format),
                    SizedBox(height: 35),

                    // 4. Fund Accounts (Structured Parent-Child Currencies list)
                    _buildSectionHeader('حسابات الصندوق المالية'),
                    SizedBox(height: 15),
                    _buildAccountsSection(format),
                    SizedBox(height: 35),

                    // 5. Tangible / Real Estate Assets Section (الأصول)
                    _buildSectionHeader('الأصول العقارية والعينية'),
                    SizedBox(height: 15),
                    _buildAssetsSection(format),
                    SizedBox(height: 35),

                    // 6. Partners & Equity Distribution (توزيع الحصص والشركاء)
                    _buildSectionHeader('توزيع الحصص والشركاء'),
                    SizedBox(height: 15),
                    _buildEquitiesSection(format),
                    SizedBox(height: 35),

                    // 7. Distributed Profits History (الأرباح الموزعة)
                    _buildSectionHeader('سجل توزيع الأرباح'),
                    SizedBox(height: 15),
                    _buildDistributionsSection(format),
                    SizedBox(height: 35),

                    // 8. Recent Fund Transactions
                    _buildSectionHeader('آخر العمليات والتدفقات'),
                    SizedBox(height: 15),
                    _buildTransactionsSection(format),
                    SizedBox(height: 60),
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

  Widget _buildHeaderHeroCard(dynamic fund, NumberFormat format) {
    final String currency = fund?['currency'] ?? 'USD';
    final double currentValue = double.tryParse((data?['current_value'] ?? fund?['current_value'] ?? '0').toString()) ?? 0.0;

    return Container(
      padding: EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF1E1B4B), Color(0xFF312E81), Color(0xFF4338CA)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            color: Color(0xFF4338CA).withOpacity(0.2),
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
              right: -40,
              bottom: -40,
              child: CircleAvatar(
                radius: 90,
                backgroundColor: Colors.white.withOpacity(0.03),
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'القيمة التقديرية الحالية للكيان',
                      style: GoogleFonts.almarai(
                        color: Colors.white.withOpacity(0.7),
                        fontWeight: FontWeight.bold,
                        fontSize: 11,
                        letterSpacing: 0.5,
                      ),
                    ),
                    Container(
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        fund?['icon'] ?? '🏘️',
                        style: TextStyle(fontSize: 16),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 10),
                Text(
                  '${format.format(currentValue)} $currency',
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
                    _buildHeroMiniStat('رأس مال الصندوق', '${format.format(fund?['capital'] ?? 0)} $currency'),
                    _buildHeroMiniStat('توزيع الأرباح', fund?['distribution_frequency'] ?? 'غير محدد'),
                    _buildHeroMiniStat('الحالة', fund?['status'] == 'active' ? 'نشط' : 'مكتمل'),
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
          style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 9, fontWeight: FontWeight.bold),
        ),
        SizedBox(height: 3),
        Text(
          val,
          style: GoogleFonts.almarai(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w800),
        ),
      ],
    );
  }

  Widget _buildQuickActions(dynamic fund) {
    return Row(
      children: [
        Expanded(
          child: _actionButton(
            label: 'تسجيل حركة',
            icon: Icons.add_circle_outline_rounded,
            color: Color(0xFF4338CA),
            onTap: () async {
              final res = await Get.to(() => AddTransactionScreen(), arguments: {
                'accountType': 'fund',
                'accountId': widget.fundId,
              });
              if (res == true) loadDetail();
            },
          ),
        ),
        SizedBox(width: 15),
        Expanded(
          child: _actionButton(
            label: 'تحويل داخلي',
            icon: Icons.swap_horizontal_circle_outlined,
            color: Color(0xFFD97706),
            onTap: () async {
              final res = await Get.to(() => TransferScreen(
                    sourceId: widget.fundId,
                    sourceType: 'InvestmentFund',
                    paymentMethods: data?['payment_methods'] ?? [],
                  ));
              if (res == true) loadDetail();
            },
          ),
        ),
      ],
    );
  }

  Widget _actionButton({required String label, required IconData icon, required Color color, required VoidCallback onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(
          color: color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: color.withOpacity(0.15), width: 1.5),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: color, size: 20),
            SizedBox(width: 8),
            Text(
              label,
              style: GoogleFonts.almarai(color: color, fontWeight: FontWeight.w900, fontSize: 13),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsGrid(NumberFormat format) {
    final String currency = data?['fund']?['currency'] ?? 'USD';
    final double investedCapital = double.tryParse((data?['total_invested_capital'] ?? '0').toString()) ?? 0.0;
    final double currentValue = double.tryParse((data?['current_value'] ?? '0').toString()) ?? 0.0;
    final double assetValue = double.tryParse((data?['total_asset_value'] ?? '0').toString()) ?? 0.0;
    final double capitalMovements = double.tryParse((data?['capital_movements'] ?? '0').toString()) ?? 0.0;
    final double netProfit = double.tryParse((data?['net_profit'] ?? '0').toString()) ?? 0.0;

    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _buildMiniStatCard('رأس المال المستثمر', investedCapital, currency, format, Color(0xFF0284C7), Color(0xFFE0F2FE)),
            ),
            SizedBox(width: 12),
            Expanded(
              child: _buildMiniStatCard('القيمة السوقية', currentValue, currency, format, Color(0xFFD97706), Colors.amber.shade50),
            ),
          ],
        ),
        SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _buildMiniStatCard('قيمة الأصول العينية', assetValue, currency, format, Color(0xFF7E22CE), Colors.purple.shade50),
            ),
            SizedBox(width: 12),
            Expanded(
              child: _buildMiniStatCard('حركات رأس المال', capitalMovements, currency, format, Color(0xFF4B5563), Colors.grey.shade100),
            ),
          ],
        ),
        SizedBox(height: 12),
        _buildFullWidthProfitCard(netProfit, currency, format),
      ],
    );
  }

  Widget _buildMiniStatCard(String label, double value, String currency, NumberFormat format, Color color, Color bg) {
    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 10, offset: Offset(0, 4))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  label,
                  style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 10),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              Container(
                width: 6,
                height: 6,
                decoration: BoxDecoration(color: color, shape: BoxShape.circle),
              )
            ],
          ),
          SizedBox(height: 8),
          Text(
            format.format(value),
            style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 16),
          ),
          SizedBox(height: 3),
          Text(
            currency,
            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 9),
          ),
        ],
      ),
    );
  }

  Widget _buildFullWidthProfitCard(double netProfit, String currency, NumberFormat format) {
    final bool isPositive = netProfit >= 0;
    final Color mainColor = isPositive ? Color(0xFF059669) : Color(0xFFDC2626);
    final Color borderColor = isPositive ? Color(0xFFA7F3D0) : Color(0xFFFCA5A5);
    final Color bgColor = isPositive ? Color(0xFFECFDF5) : Color(0xFFFFF1F2);

    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: borderColor, width: 1.5),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'صافي الأرباح / الخسائر التشغيلية',
                style: GoogleFonts.almarai(color: mainColor.withOpacity(0.8), fontWeight: FontWeight.bold, fontSize: 10),
              ),
              SizedBox(height: 4),
              Text(
                '${isPositive ? '+' : ''}${format.format(netProfit)} $currency',
                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: mainColor, fontSize: 20),
              ),
            ],
          ),
          Container(
            padding: EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle),
            child: Icon(
              isPositive ? Icons.trending_up : Icons.trending_down,
              color: mainColor,
              size: 20,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAccountsSection(NumberFormat format) {
    final accounts = data?['payment_methods'] as List? ?? [];
    if (accounts.isEmpty) {
      return _buildEmptyState('لا توجد حسابات مالية مضافة لهذا الصندوق');
    }

    return Column(
      children: accounts.map((parent) {
        final childrenList = parent['children'] as List? ?? [];
        return Container(
          margin: EdgeInsets.only(bottom: 15),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 10, offset: Offset(0, 5))
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Parent Header Renders Bank or Wallet summary
              Padding(
                padding: EdgeInsets.all(18),
                child: Row(
                  children: [
                    Container(
                      padding: EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: Colors.indigo.shade50,
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Text(
                        parent['type'] == 'bank' ? '🏦' : '💵',
                        style: TextStyle(fontSize: 18),
                      ),
                    ),
                    SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            parent['name'],
                            style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 13),
                          ),
                          SizedBox(height: 2),
                          Text(
                            parent['type'] == 'bank' ? 'حساب بنكي مجمع' : 'نقد وصندوق الكيان',
                            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 10),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${childrenList.length} عملات',
                        style: GoogleFonts.almarai(color: Color(0xFF475569), fontWeight: FontWeight.bold, fontSize: 9),
                      ),
                    )
                  ],
                ),
              ),

              if (childrenList.isNotEmpty) Divider(color: Color(0xFFF1F5F9), height: 1, thickness: 1),

              // Children Sub-Accounts (Different Currencies)
              ...childrenList.map((child) {
                return Container(
                  padding: EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                  color: Color(0xFFF8FAFC).withOpacity(0.5),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 6,
                            height: 6,
                            decoration: BoxDecoration(color: Colors.indigo, shape: BoxShape.circle),
                          ),
                          SizedBox(width: 10),
                          Text(
                            'حساب ${child['currency']}',
                            style: GoogleFonts.almarai(fontWeight: FontWeight.bold, color: Color(0xFF475569), fontSize: 11),
                          ),
                        ],
                      ),
                      Text(
                        '${format.format(child['balance'] ?? 0)} ${child['currency']}',
                        style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 13),
                      ),
                    ],
                  ),
                );
              }).toList(),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildAssetsSection(NumberFormat format) {
    final assets = data?['fund']?['assets'] as List? ?? [];
    if (assets.isEmpty) {
      return _buildEmptyState('لا توجد أصول عقارية أو عينية مسجلة لهذا الصندوق');
    }

    return Column(
      children: assets.map((asset) {
        final double val = double.tryParse((asset['value'] ?? '0').toString()) ?? 0.0;
        final String currency = data?['fund']?['currency'] ?? 'USD';

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 10, offset: Offset(0, 4))
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.amber.shade50,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(
                  child: Icon(Icons.location_city_rounded, color: Colors.amber.shade700, size: 20),
                ),
              ),
              SizedBox(width: 15),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      asset['name'] ?? 'أصل عقاري',
                      style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0F172A)),
                    ),
                    if (asset['notes'] != null && asset['notes'].toString().isNotEmpty)
                      Padding(
                        padding: const EdgeInsets.only(top: 3.0),
                        child: Text(
                          asset['notes'],
                          style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                  ],
                ),
              ),
              Text(
                '${format.format(val)} $currency',
                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 14),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildEquitiesSection(NumberFormat format) {
    final equities = data?['equities'] as List? ?? [];
    if (equities.isEmpty) {
      return _buildEmptyState('لا يوجد شركاء مسجلين في هذا الكيان');
    }

    return Column(
      children: equities.map((e) {
        final double percentage = double.tryParse((e['percentage'] ?? '0').toString()) ?? 0.0;
        final String partnerName = e['partner']?['name'] ?? 'شريك';
        final String equityType = e['equity_type'] ?? 'fixed';

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 10, offset: Offset(0, 4))
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.purple.shade50,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(
                  child: Text(
                    partnerName[0].toUpperCase(),
                    style: GoogleFonts.almarai(color: Colors.purple.shade700, fontWeight: FontWeight.w900, fontSize: 16),
                  ),
                ),
              ),
              SizedBox(width: 15),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      partnerName,
                      style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF0F172A)),
                    ),
                    SizedBox(height: 3),
                    Text(
                      equityType == 'contribution' ? 'مساهمة رأسمالية نشطة' : 'نسبة مئوية ثابتة',
                      style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              Container(
                padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.purple.shade50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  '${percentage.toStringAsFixed(1)}%',
                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.purple.shade700, fontSize: 13),
                ),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildDistributionsSection(NumberFormat format) {
    final distributions = data?['fund']?['distributions'] as List? ?? [];
    if (distributions.isEmpty) {
      return _buildEmptyState('لم يتم توزيع أرباح لهذا الصندوق بعد');
    }

    return Column(
      children: distributions.map((dist) {
        final double amount = double.tryParse((dist['amount'] ?? '0').toString()) ?? 0.0;
        final String currency = data?['fund']?['currency'] ?? 'USD';
        final String dateStr = dist['created_at'] != null
            ? DateFormat('yyyy-MM-dd').format(DateTime.parse(dist['created_at']))
            : 'تاريخ غير معروف';

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 38,
                    height: 38,
                    decoration: BoxDecoration(
                      color: Colors.green.shade50,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Icon(Icons.payments_outlined, color: Colors.green.shade700, size: 18),
                    ),
                  ),
                  SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'توزيع أرباح منفذ',
                        style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 12, color: Color(0xFF0F172A)),
                      ),
                      SizedBox(height: 2),
                      Text(
                        dateStr,
                        style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 9, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ],
              ),
              Text(
                '${format.format(amount)} $currency',
                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.green.shade700, fontSize: 13),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildTransactionsSection(NumberFormat format) {
    final txs = data?['recent_transactions'] as List? ?? [];
    if (txs.isEmpty) {
      return _buildEmptyState('لا توجد عمليات مسجلة حالياً');
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
            : (data?['fund']?['currency'] ?? 'USD');

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
