import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../api/api_service.dart';
import 'fund_detail_screen.dart';

class FundsScreen extends StatefulWidget {
  @override
  _FundsScreenState createState() => _FundsScreenState();
}

class _FundsScreenState extends State<FundsScreen> {
  final apiService = ApiService();
  List funds = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadFunds();
  }

  void loadFunds() async {
    final result = await apiService.getFunds();
    setState(() {
      funds = result ?? [];
      isLoading = false;
    });
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
                  fund == null ? 'كيان استثماري جديد' : 'تعديل الكيان الاستثماري',
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
                  bool success;
                  if (fund == null) {
                    success = await apiService.addFund(data);
                  } else {
                    success = await apiService.updateFund(fund['id'], data);
                  }
                  if (success) {
                    Get.back();
                    loadFunds();
                    Get.snackbar('تم بنجاح', 'تم حفظ الكيان الاستثماري بنجاح');
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
    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          'صناديق الاستثمار',
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
          ? Center(child: CircularProgressIndicator(color: Colors.indigo))
          : RefreshIndicator(
              onRefresh: () async => loadFunds(),
              color: Colors.indigo,
              child: ListView.builder(
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                itemCount: funds.length,
                itemBuilder: (context, i) {
                  final f = funds[i];
                  return GestureDetector(
                    onLongPress: () => _showFundDialog(fund: f),
                    onTap: () => Get.to(() => FundDetailScreen(fundId: f['id']))!.then((_) => loadFunds()),
                    child: Container(
                      margin: EdgeInsets.only(bottom: 18),
                      padding: EdgeInsets.all(22),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(color: Color(0xFFE9D5FF).withOpacity(0.4), width: 1.5),
                        boxShadow: [
                          BoxShadow(color: Color(0xFF7E22CE).withOpacity(0.02), blurRadius: 15, offset: Offset(0, 8))
                        ],
                      ),
                      child: Column(
                        children: [
                          Row(
                            children: [
                              Container(
                                width: 55,
                                height: 55,
                                decoration: BoxDecoration(
                                  color: Color(0xFFFAF5FF),
                                  borderRadius: BorderRadius.circular(18),
                                  border: Border.all(color: Color(0xFFE9D5FF), width: 1.5),
                                ),
                                child: Center(child: Text(f['icon'] ?? '🏢', style: TextStyle(fontSize: 24))),
                              ),
                              SizedBox(width: 15),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      f['name'],
                                      style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF581C87)),
                                    ),
                                    SizedBox(height: 3),
                                    Text(
                                      f['status'] == 'active' ? 'صندوق استثماري نشط' : 'كيان مكتمل / مغلق',
                                      style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                          SizedBox(height: 20),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'القيمة السوقية المقدرة',
                                    style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
                                  ),
                                  SizedBox(height: 4),
                                  Text(
                                    '${f['current_value']} ${f['currency']}',
                                    style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFF0F172A), letterSpacing: -0.5),
                                  ),
                                ],
                              ),
                              Container(
                                padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: f['status'] == 'active' ? Color(0xFFECFDF5) : Color(0xFFF1F5F9),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  f['status'] == 'active' ? 'نشط' : 'مكتمل',
                                  style: GoogleFonts.almarai(
                                    color: f['status'] == 'active' ? Color(0xFF047857) : Color(0xFF475569),
                                    fontWeight: FontWeight.bold,
                                    fontSize: 10,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showFundDialog(),
        backgroundColor: Colors.indigo,
        elevation: 4,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
