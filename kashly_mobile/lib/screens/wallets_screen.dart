import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';
import 'wallet_detail_screen.dart';

class WalletsScreen extends StatefulWidget {
  @override
  _WalletsScreenState createState() => _WalletsScreenState();
}

class _WalletsScreenState extends State<WalletsScreen> {
  final apiService = ApiService();
  List wallets = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  void loadData() async {
    final result = await apiService.getWallets();
    setState(() {
      wallets = result ?? [];
      isLoading = false;
    });
  }

  void _showWalletDialog({Map? wallet}) {
    final nameController = TextEditingController(text: wallet?['name']);
    final balanceController = TextEditingController(text: wallet?['balance']?.toString() ?? '0');
    final custodianController = TextEditingController(text: wallet?['custodian_name']);
    String currency = wallet?['currency'] ?? 'USD';

    // Type of account: 'bank' if custodian_name is empty/null, else 'custody'
    String selectedType = (wallet?['custodian_name']?.toString().isNotEmpty == true) ? 'custody' : 'bank';

    Get.bottomSheet(
      StatefulBuilder(
        builder: (context, setSheetState) {
          return Container(
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
                      wallet == null ? 'إضافة حساب أو عهدة جديدة' : 'تعديل البيانات',
                      style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0284C7)),
                    ),
                  ),
                  SizedBox(height: 20),

                  // Segmented wallet type selector
                  Container(
                    padding: EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: GestureDetector(
                            onTap: () {
                              setSheetState(() => selectedType = 'bank');
                            },
                            child: Container(
                              padding: EdgeInsets.symmetric(vertical: 10),
                              decoration: BoxDecoration(
                                color: selectedType == 'bank' ? Colors.white : Colors.transparent,
                                borderRadius: BorderRadius.circular(12),
                                boxShadow: selectedType == 'bank'
                                    ? [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4, offset: Offset(0, 2))]
                                    : null,
                              ),
                              child: Center(
                                child: Text('💳 حساب بنكي / ائتمان',
                                    style: GoogleFonts.almarai(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w900,
                                        color: selectedType == 'bank' ? Color(0xFF0284C7) : Color(0xFF64748B))),
                              ),
                            ),
                          ),
                        ),
                        Expanded(
                          child: GestureDetector(
                            onTap: () {
                              setSheetState(() => selectedType = 'custody');
                            },
                            child: Container(
                              padding: EdgeInsets.symmetric(vertical: 10),
                              decoration: BoxDecoration(
                                color: selectedType == 'custody' ? Colors.white : Colors.transparent,
                                borderRadius: BorderRadius.circular(12),
                                boxShadow: selectedType == 'custody'
                                    ? [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4, offset: Offset(0, 2))]
                                    : null,
                              ),
                              child: Center(
                                child: Text('💼 عهدة مالية شخصية',
                                    style: GoogleFonts.almarai(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w900,
                                        color: selectedType == 'custody' ? Color(0xFF0284C7) : Color(0xFF64748B))),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  SizedBox(height: 20),

                  TextField(
                    controller: nameController,
                    style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.bold),
                    decoration: InputDecoration(
                      labelText: selectedType == 'bank' ? 'اسم الحساب البنكي / بطاقة الائتمان' : 'اسم محفظة العهدة',
                      labelStyle: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                      filled: true,
                      fillColor: Color(0xFFF8FAFC),
                      enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                      focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFF0284C7), width: 1.5)),
                      contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                    ),
                  ),
                  
                  if (selectedType == 'custody') ...[
                    SizedBox(height: 15),
                    TextField(
                      controller: custodianController,
                      style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.bold),
                      decoration: InputDecoration(
                        labelText: 'اسم أمين العهدة (الشخص المسؤول) *',
                        labelStyle: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                        filled: true,
                        fillColor: Color(0xFFF8FAFC),
                        enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                        focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFF0284C7), width: 1.5)),
                        contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                      ),
                    ),
                  ],
                  SizedBox(height: 15),

                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: balanceController,
                          keyboardType: TextInputType.numberWithOptions(decimal: true),
                          style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.black),
                          decoration: InputDecoration(
                            labelText: 'الرصيد الافتتاحي',
                            labelStyle: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFF0284C7), width: 1.5)),
                            contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                          ),
                        ),
                      ),
                      SizedBox(width: 15),
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: currency,
                          dropdownColor: Colors.white,
                          style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                          decoration: InputDecoration(
                            labelText: 'العملة الأساسية',
                            labelStyle: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFF0284C7), width: 1.5)),
                            contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                          ),
                          items: ['USD', 'SYP', 'AED', 'SAR', 'EUR', 'TRY'].map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                          onChanged: (val) => currency = val!,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 30),
                  ElevatedButton(
                    onPressed: () async {
                      if (selectedType == 'custody' && custodianController.text.trim().isEmpty) {
                        Get.snackbar('حقول ناقصة', 'يرجى إدخال اسم أمين العهدة للعهد المالية',
                            backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
                        return;
                      }

                      final data = {
                        'name': nameController.text,
                        'balance': balanceController.text,
                        'custodian_name': selectedType == 'bank' ? '' : custodianController.text,
                        'currency': currency,
                      };
                      
                      bool success;
                      if (wallet == null) {
                        success = await apiService.addWallet(data);
                      } else {
                        success = await apiService.updateWallet(wallet['id'], data);
                      }
                      
                      if (success) {
                        Get.back();
                        loadData();
                        Get.snackbar('تمت العملية', 'تم حفظ البيانات بنجاح',
                            backgroundColor: Colors.green.shade50, colorText: Colors.green.shade800);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Color(0xFF0284C7),
                      minimumSize: Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                      elevation: 3,
                      shadowColor: Color(0xFF0284C7).withOpacity(0.3),
                    ),
                    child: Text(
                      wallet == null ? '✓ إنشاء الحساب' : '✓ حفظ التغييرات',
                      style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 14),
                    ),
                  )
                ],
              ),
            ),
          );
        }
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final format = NumberFormat('#,##0.00', 'en_US');

    // Split wallets into accounts (empty custodian_name) and custodies
    final bankAccounts = wallets.where((w) => (w['custodian_name'] ?? '').toString().trim().isEmpty).toList();
    final custodies = wallets.where((w) => (w['custodian_name'] ?? '').toString().trim().isNotEmpty).toList();

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'المحافظ الشخصية',
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        surfaceTintColor: Colors.transparent,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: Color(0xFF0F172A), size: 18),
          onPressed: () => Navigator.pop(context),
        ),
        bottom: PreferredSize(
            preferredSize: Size.fromHeight(1),
            child: Divider(height: 1, color: Color(0xFFE2E8F0))),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : RefreshIndicator(
              onRefresh: () async => loadData(),
              color: Color(0xFF0284C7),
              child: CustomScrollView(
                physics: BouncingScrollPhysics(),
                slivers: [
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(20, 20, 20, 100),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        
                        // ── SECTION 1: الحسابات البنكية والائتمانية ──
                        Row(children: [
                          Container(width: 3, height: 14, decoration: BoxDecoration(color: Color(0xFF0284C7), borderRadius: BorderRadius.circular(2))),
                          SizedBox(width: 8),
                          Text('💳 الحسابات البنكية والبطاقات الائتمانية',
                              style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF0C4A6E))),
                          SizedBox(width: 6),
                          Text('(${bankAccounts.length})',
                              style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                        ]),
                        SizedBox(height: 12),
                        if (bankAccounts.isEmpty)
                          _emptySection('لا توجد حسابات بنكية مسجلة')
                        else
                          ...bankAccounts.map((w) => _buildWalletCard(w, format, isBank: true)).toList(),
                        
                        SizedBox(height: 30),

                        // ── SECTION 2: العهد المالية ──
                        Row(children: [
                          Container(width: 3, height: 14, decoration: BoxDecoration(color: Color(0xFFD97706), borderRadius: BorderRadius.circular(2))),
                          SizedBox(width: 8),
                          Text('💼 العهد الشخصية والأموال السائلة',
                              style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF9A3412))),
                          SizedBox(width: 6),
                          Text('(${custodies.length})',
                              style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                        ]),
                        SizedBox(height: 12),
                        if (custodies.isEmpty)
                          _emptySection('لا توجد عهد مالية شخصية مسجلة')
                        else
                          ...custodies.map((w) => _buildWalletCard(w, format, isBank: false)).toList(),
                      ]),
                    ),
                  )
                ],
              ),
            ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showWalletDialog(),
        backgroundColor: Color(0xFF0284C7),
        elevation: 4,
        icon: Icon(Icons.add_rounded, color: Colors.white),
        label: Text('إضافة جديد',
            style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 13)),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }

  Widget _emptySection(String text) {
    return Container(
      padding: EdgeInsets.symmetric(vertical: 30),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Color(0xFFE2E8F0)),
      ),
      child: Center(
        child: Text(text,
            style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
      ),
    );
  }

  Widget _buildWalletCard(Map w, NumberFormat format, {required bool isBank}) {
    final String custodianName = w['custodian_name'] ?? '';
    final remaining = double.tryParse(w['balance']?.toString() ?? '0') ?? 0.0;

    return GestureDetector(
      onLongPress: () => _showWalletDialog(wallet: w),
      onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id']))!.then((_) => loadData()),
      child: Container(
        margin: EdgeInsets.only(bottom: 12),
        padding: EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: isBank ? Color(0xFFBAE6FD).withOpacity(0.5) : Color(0xFFFED7AA).withOpacity(0.5), width: 1.5),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 10, offset: Offset(0, 3))
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isBank ? Color(0xFFF0F9FF) : Color(0xFFFFF7ED),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: isBank ? Color(0xFFBAE6FD) : Color(0xFFFED7AA), width: 1.5),
              ),
              child: Icon(
                isBank ? Icons.credit_card_rounded : Icons.wallet_rounded,
                color: isBank ? Color(0xFF0284C7) : Color(0xFFD97706),
                size: 22,
              ),
            ),
            SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    w['name'],
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w900,
                      fontSize: 14,
                      color: isBank ? Color(0xFF0C4A6E) : Color(0xFF7C2D12),
                    ),
                  ),
                  SizedBox(height: 3),
                  Text(
                    !isBank ? '🔑 أمين العهدة: $custodianName' : '🏦 حساب بنكي مباشر',
                    style: GoogleFonts.almarai(
                      color: !isBank ? Color(0xFFD97706) : Color(0xFF64748B),
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  '${format.format(remaining)} ${w['currency']}',
                  style: GoogleFonts.almarai(
                    fontWeight: FontWeight.w900,
                    fontSize: 16,
                    color: isBank ? Color(0xFF0284C7) : Color(0xFFD97706),
                  ),
                ),
                Text(
                  'الرصيد المتاح',
                  style: GoogleFonts.almarai(fontSize: 8, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }
}
