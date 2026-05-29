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
    bool isCustody = (wallet?['custodian_name'] ?? '').toString().isNotEmpty;

    Get.bottomSheet(
      StatefulBuilder(
        builder: (context, setState) {
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
                      wallet == null ? 'إضافة محفظة / عهدة جديدة' : 'تعديل المحفظة / العهدة',
                      style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0369A1)),
                    ),
                  ),
                  SizedBox(height: 20),

                  // Wallet Type Toggle
                  Row(
                    children: [
                      Expanded(
                        child: ChoiceChip(
                          label: Container(
                            width: double.infinity,
                            alignment: Alignment.center,
                            child: Text('👛 محفظة شخصية', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 12)),
                          ),
                          selected: !isCustody,
                          selectedColor: Color(0xFFE0F2FE),
                          onSelected: (val) {
                            if (val) setState(() {
                              isCustody = false;
                              custodianController.clear();
                            });
                          },
                        ),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: ChoiceChip(
                          label: Container(
                            width: double.infinity,
                            alignment: Alignment.center,
                            child: Text('💼 عهدة بعهدة شخص', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 12)),
                          ),
                          selected: isCustody,
                          selectedColor: Color(0xFFFEF3C7),
                          onSelected: (val) {
                            if (val) setState(() => isCustody = true);
                          },
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 20),

                  TextField(
                    controller: nameController,
                    decoration: InputDecoration(
                      labelText: isCustody ? 'اسم العهدة المستقلة' : 'اسم المحفظة الشخصية',
                      labelStyle: GoogleFonts.almarai(fontSize: 12),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                    ),
                  ),
                  if (isCustody) ...[
                    SizedBox(height: 15),
                    TextField(
                      controller: custodianController,
                      decoration: InputDecoration(
                        labelText: 'اسم الشخص المسؤول عن العهدة *',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ],
                  SizedBox(height: 15),
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: balanceController,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                            labelText: 'الرصيد الافتتاحي',
                            labelStyle: GoogleFonts.almarai(fontSize: 12),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                          ),
                        ),
                      ),
                      SizedBox(width: 15),
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: currency,
                          decoration: InputDecoration(
                            labelText: 'العملة الأساسية',
                            labelStyle: GoogleFonts.almarai(fontSize: 12),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                          ),
                          items: ['USD', 'SYP', 'AED', 'SAR', 'TRY', 'EUR'].map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                          onChanged: (val) => currency = val!,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 30),
                  ElevatedButton(
                    onPressed: () async {
                      if (isCustody && custodianController.text.trim().isEmpty) {
                        Get.snackbar('خطأ', 'يرجى إدخال اسم الشخص المسؤول عن العهدة');
                        return;
                      }
                      final data = {
                        'name': nameController.text,
                        'balance': balanceController.text,
                        'custodian_name': isCustody ? custodianController.text : null,
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
                        Get.snackbar('تمت العملية', 'تم حفظ البيانات بنجاح');
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Color(0xFF0284C7),
                      minimumSize: Size(double.infinity, 56),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    ),
                    child: Text(
                      'حفظ البيانات',
                      style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15),
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
    final format = NumberFormat('#,##0', 'en_US');
    final personalWallets = wallets.where((w) => (w['custodian_name'] ?? '').toString().isEmpty).toList();
    final custodyWallets = wallets.where((w) => (w['custodian_name'] ?? '').toString().isNotEmpty).toList();

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          'المحافظ والعهد المالية',
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios, color: Color(0xFF0F172A), size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : RefreshIndicator(
              onRefresh: () async => loadData(),
              color: Color(0xFF0284C7),
              child: ListView(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                children: [
                  // 1. Personal Wallets
                  if (personalWallets.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.only(bottom: 12, top: 8),
                      child: Text(
                        '👛 المحافظ الشخصية المستقلة',
                        style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFF0F172A)),
                      ),
                    ),
                    ...personalWallets.map((w) => _buildWalletCard(w, format, false)),
                    SizedBox(height: 15),
                  ],

                  // 2. Custody Wallets
                  if (custodyWallets.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.only(bottom: 12, top: 8),
                      child: Text(
                        '💼 العهد المالية بعهدة أشخاص',
                        style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14, color: Color(0xFFB45309)),
                      ),
                    ),
                    ...custodyWallets.map((w) => _buildWalletCard(w, format, true)),
                    SizedBox(height: 15),
                  ],

                  if (wallets.isEmpty)
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: 80),
                        child: Column(
                          children: [
                            Text('🕳️', style: TextStyle(fontSize: 40)),
                            SizedBox(height: 10),
                            Text('لا توجد محافظ أو عهد حالياً', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, color: Color(0xFF94A3B8))),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showWalletDialog(),
        backgroundColor: Color(0xFF0284C7),
        elevation: 4,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildWalletCard(dynamic w, NumberFormat format, bool isCustody) {
    final String custodianName = w['custodian_name'] ?? '';
    return GestureDetector(
      onLongPress: () => _showWalletDialog(wallet: w),
      onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id']))!.then((_) => loadData()),
      child: Container(
        margin: EdgeInsets.only(bottom: 12),
        padding: EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(
            color: isCustody ? Color(0xFFFDE68A) : Color(0xFFBAE6FD).withOpacity(0.4),
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 10, offset: Offset(0, 4))
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isCustody ? Color(0xFFFEF3C7) : Color(0xFFF0F9FF),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(
                isCustody ? Icons.folder_shared_rounded : Icons.account_balance_wallet_outlined,
                color: isCustody ? Color(0xFFD97706) : Color(0xFF0284C7),
                size: 22,
              ),
            ),
            SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    w['name'],
                    style: GoogleFonts.almarai(
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                      color: isCustody ? Color(0xFF78350F) : Color(0xFF0C4A6E),
                    ),
                  ),
                  SizedBox(height: 3),
                  Text(
                    isCustody ? 'بعهدة أمين العهدة: $custodianName' : 'محفظة مالية شخصية',
                    style: GoogleFonts.almarai(
                      color: isCustody ? Color(0xFFD97706) : Color(0xFF94A3B8),
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
                  '${format.format(double.tryParse(w['balance']?.toString() ?? '0') ?? 0.0)} ${w['currency']}',
                  style: GoogleFonts.almarai(
                    fontWeight: FontWeight.w900,
                    fontSize: 16,
                    color: isCustody ? Color(0xFFB45309) : Color(0xFF0284C7),
                    letterSpacing: -0.5,
                  ),
                ),
                SizedBox(height: 2),
                Text(
                  'الرصيد الحالي',
                  style: GoogleFonts.almarai(fontSize: 8, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showWalletDialog(),
        backgroundColor: Color(0xFF0284C7),
        elevation: 4,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
