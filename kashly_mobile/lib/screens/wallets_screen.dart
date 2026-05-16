import 'package:flutter/material.dart';
import 'package:get/get.dart';
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
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(35))),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(wallet == null ? 'محفظة جديدة' : 'تعديل المحفظة', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.lightBlue.shade900)),
              SizedBox(height: 20),
              TextField(controller: nameController, decoration: InputDecoration(labelText: 'اسم المحفظة', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)))),
              SizedBox(height: 15),
              TextField(controller: custodianController, decoration: InputDecoration(labelText: 'اسم المسؤول (اختياري)', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)))),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(child: TextField(controller: balanceController, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: 'الرصيد', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))))),
                  SizedBox(width: 15),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: currency,
                      decoration: InputDecoration(border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))),
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
                    Get.snackbar('تم', 'تم حفظ المحفظة بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(backgroundColor: Colors.lightBlue, minimumSize: Size(double.infinity, 60), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
                child: Text('حفظ', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
              )
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('المحافظ الشخصية', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.lightBlue.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : RefreshIndicator(
            onRefresh: () async => loadData(),
            child: ListView.builder(
              padding: EdgeInsets.all(20),
              itemCount: wallets.length,
              itemBuilder: (context, i) {
                final w = wallets[i];
                return GestureDetector(
                  onLongPress: () => _showWalletDialog(wallet: w),
                  onTap: () => Get.to(() => WalletDetailScreen(walletId: w['id'])),
                  child: Container(
                    margin: EdgeInsets.only(bottom: 20),
                    padding: EdgeInsets.all(25),
                    decoration: BoxDecoration(
                      color: Colors.lightBlue.shade50,
                      borderRadius: BorderRadius.circular(35),
                      border: Border.all(color: Colors.lightBlue.shade200, width: 2),
                      boxShadow: [BoxShadow(color: Colors.lightBlue.shade100.withOpacity(0.5), blurRadius: 20, offset: Offset(0, 10))],
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: EdgeInsets.all(15),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
                          child: Icon(Icons.account_balance_wallet, color: Colors.lightBlue.shade600, size: 30),
                        ),
                        SizedBox(width: 20),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(w['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Colors.lightBlue.shade900)),
                              SizedBox(height: 5),
                              Text(w['custodian_name'] ?? 'محفظة نشطة', style: TextStyle(color: Colors.lightBlue.shade400, fontWeight: FontWeight.bold, fontSize: 13)),
                            ],
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text('${w['balance']} ${w['currency']}', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 22, color: Colors.lightBlue.shade700)),
                            Text('الرصيد الحالي', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.lightBlue.shade300)),
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
        backgroundColor: Colors.lightBlue,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
