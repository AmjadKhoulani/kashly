import 'package:flutter/material.dart';
import 'package:get/get.dart';
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
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(35))),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(business == null ? 'نشاط تجاري جديد' : 'تعديل النشاط', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.amber.shade900)),
              SizedBox(height: 20),
              TextField(controller: nameController, decoration: InputDecoration(labelText: 'اسم النشاط التجاري', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)))),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(child: TextField(controller: valueController, keyboardType: TextInputType.number, decoration: InputDecoration(labelText: 'القيمة التقديرية', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))))),
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
                    Get.snackbar('تم', 'تم حفظ النشاط التجاري بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(backgroundColor: Colors.amber, minimumSize: Size(double.infinity, 60), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
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
        title: Text('قطاع الأعمال', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.amber.shade900)),
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
              itemCount: businesses.length,
              itemBuilder: (context, i) {
                final b = businesses[i];
                return GestureDetector(
                  onLongPress: () => _showBusinessDialog(business: b),
                  onTap: () => Get.to(() => BusinessDetailScreen(businessId: b['id'])),
                  child: Container(
                    margin: EdgeInsets.only(bottom: 20),
                    padding: EdgeInsets.all(25),
                    decoration: BoxDecoration(
                      color: Colors.amber.shade50,
                      borderRadius: BorderRadius.circular(35),
                      border: Border.all(color: Colors.amber.shade200, width: 2),
                      boxShadow: [BoxShadow(color: Colors.amber.shade100.withOpacity(0.5), blurRadius: 20, offset: Offset(0, 10))],
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: EdgeInsets.all(15),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
                          child: Icon(Icons.storefront, color: Colors.amber.shade600, size: 30),
                        ),
                        SizedBox(width: 20),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(b['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Colors.amber.shade900)),
                              SizedBox(height: 5),
                              Text('نشاط تجاري نشط', style: TextStyle(color: Colors.amber.shade400, fontWeight: FontWeight.bold, fontSize: 13)),
                            ],
                          ),
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text('${b['total_value']} ${b['currency'] ?? 'USD'}', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 22, color: Colors.amber.shade700)),
                            Text('القيمة الحالية', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.amber.shade300)),
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
        backgroundColor: Colors.amber,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
