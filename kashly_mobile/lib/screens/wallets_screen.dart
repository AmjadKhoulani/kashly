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
                  wallet == null ? 'إضافة محفظة جديدة' : 'تعديل المحفظة الشخصية',
                  style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0369A1)),
                ),
              ),
              SizedBox(height: 25),
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: 'اسم المحفظة العهدة',
                  labelStyle: GoogleFonts.almarai(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
              ),
              SizedBox(height: 15),
              TextField(
                controller: custodianController,
                decoration: InputDecoration(
                  labelText: 'اسم المسؤول المالي (عهدة شخصية - اختياري)',
                  labelStyle: GoogleFonts.almarai(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
              ),
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
                      items: ['USD', 'SYP', 'AED', 'SAR'].map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                      onChanged: (val) => currency = val!,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 30),
              ElevatedButton(
                onPressed: () async {
                  final data = {
                    'name': nameController.text,
                    'balance': balanceController.text,
                    'custodian_name': custodianController.text,
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
                    Get.snackbar('تمت العملية', 'تم حفظ بيانات المحفظة بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Color(0xFF0284C7),
                  minimumSize: Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                child: Text(
                  'حفظ بيانات المحفظة',
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

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          'المحافظ الشخصية',
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
              child: ListView.builder(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                itemCount: wallets.length,
                itemBuilder: (context, i) {
                  final w = wallets[i];
                  final String custodianName = w['custodian_name'] ?? '';

                  return GestureDetector(
                    onLongPress: () => _showWalletDialog(wallet: w),
                    onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id']))!.then((_) => loadData()),
                    child: Container(
                      margin: EdgeInsets.only(bottom: 18),
                      padding: EdgeInsets.all(22),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(color: Color(0xFFBAE6FD).withOpacity(0.4), width: 1.5),
                        boxShadow: [
                          BoxShadow(color: Color(0xFF0284C7).withOpacity(0.02), blurRadius: 15, offset: Offset(0, 8))
                        ],
                      ),
                      child: Row(
                        children: [
                          Container(
                            padding: EdgeInsets.all(15),
                            decoration: BoxDecoration(
                              color: Color(0xFFF0F9FF),
                              borderRadius: BorderRadius.circular(18),
                              border: Border.all(color: Color(0xFFBAE6FD), width: 1.5),
                            ),
                            child: Icon(Icons.account_balance_wallet_outlined, color: Color(0xFF0284C7), size: 24),
                          ),
                          SizedBox(width: 18),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  w['name'],
                                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF0C4A6E)),
                                ),
                                SizedBox(height: 4),
                                Text(
                                  custodianName.isNotEmpty ? 'عهدة شخصية: $custodianName' : 'محفظة مالية أساسية',
                                  style: GoogleFonts.almarai(
                                    color: custodianName.isNotEmpty ? Color(0xFFD97706) : Color(0xFF94A3B8),
                                    fontSize: 11,
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
                                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF0284C7), letterSpacing: -0.5),
                              ),
                              SizedBox(height: 2),
                              Text(
                                'الرصيد المتاح',
                                style: GoogleFonts.almarai(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                              ),
                            ],
                          )
                        ],
                      ),
                    ),
                  );
                },
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
}
