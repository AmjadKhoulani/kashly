import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';
import 'business_detail_screen.dart';

class BusinessesScreen extends StatefulWidget {
  @override
  _BusinessesScreenState createState() => _BusinessesScreenState();
}

class _BusinessesScreenState extends State<BusinessesScreen> {
  final apiService = ApiService();
  List businesses = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  void loadData() async {
    final result = await apiService.getBusinesses();
    setState(() {
      businesses = result ?? [];
      isLoading = false;
    });
  }

  void _showBusinessDialog({Map? business}) {
    final nameController = TextEditingController(text: business?['name']);
    final valueController = TextEditingController(text: business?['total_value']?.toString() ?? '0');
    String currency = business?['currency'] ?? 'USD';

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
                  business == null ? 'نشاط تجاري جديد' : 'تعديل النشاط التجاري',
                  style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFFB45309)),
                ),
              ),
              SizedBox(height: 25),
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: 'اسم المشروع / النشاط التجاري',
                  labelStyle: GoogleFonts.almarai(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: valueController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'القيمة التقديرية الحالية',
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
                        labelText: 'عملة النشاط',
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
                    'total_value': valueController.text,
                    'currency': currency,
                  };
                  bool success;
                  if (business == null) {
                    success = await apiService.addBusiness(data);
                  } else {
                    success = await apiService.updateBusiness(business['id'], data);
                  }
                  if (success) {
                    Get.back();
                    loadData();
                    Get.snackbar('تمت العملية', 'تم حفظ بيانات النشاط التجاري بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Color(0xFFB45309),
                  minimumSize: Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                child: Text(
                  'حفظ بيانات النشاط التجاري',
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
          'قطاع الأعمال والمشاريع',
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
          ? Center(child: CircularProgressIndicator(color: Color(0xFFB45309)))
          : RefreshIndicator(
              onRefresh: () async => loadData(),
              color: Color(0xFFB45309),
              child: ListView.builder(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                itemCount: businesses.length,
                itemBuilder: (context, i) {
                  final b = businesses[i];
                  return GestureDetector(
                    onLongPress: () => _showBusinessDialog(business: b),
                    onTap: () => Get.to(() => BusinessDetailScreen(businessId: b['id']))!.then((_) => loadData()),
                    child: Container(
                      margin: EdgeInsets.only(bottom: 18),
                      padding: EdgeInsets.all(22),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(color: Color(0xFFFCD34D).withOpacity(0.4), width: 1.5),
                        boxShadow: [
                          BoxShadow(color: Color(0xFFD97706).withOpacity(0.02), blurRadius: 15, offset: Offset(0, 8))
                        ],
                      ),
                      child: Row(
                        children: [
                          Container(
                            padding: EdgeInsets.all(15),
                            decoration: BoxDecoration(
                              color: Color(0xFFFEF3C7),
                              borderRadius: BorderRadius.circular(18),
                              border: Border.all(color: Color(0xFFFCD34D), width: 1.5),
                            ),
                            child: Icon(Icons.storefront_rounded, color: Color(0xFFB45309), size: 24),
                          ),
                          SizedBox(width: 18),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  b['name'],
                                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF78350F)),
                                ),
                                SizedBox(height: 4),
                                Text(
                                  'مشروع تجاري نشط',
                                  style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                '${format.format(double.tryParse(b['total_value']?.toString() ?? '0') ?? 0.0)} ${b['currency'] ?? 'USD'}',
                                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 18, color: Color(0xFFB45309), letterSpacing: -0.5),
                              ),
                              SizedBox(height: 2),
                              Text(
                                'القيمة الحالية',
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
        onPressed: () => _showBusinessDialog(),
        backgroundColor: Color(0xFFB45309),
        elevation: 4,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
