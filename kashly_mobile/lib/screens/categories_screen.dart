import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';

class CategoriesScreen extends StatefulWidget {
  @override
  _CategoriesScreenState createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  final apiService = ApiService();
  List categories = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadCategories();
  }

  void loadCategories() async {
    final result = await apiService.getTransactionCategories();
    setState(() {
      categories = result ?? [];
      isLoading = false;
    });
  }

  void _showCategoryDialog({Map? category}) {
    final nameController = TextEditingController(text: category?['name']);
    final iconController = TextEditingController(text: category?['icon'] ?? '📁');
    final colorController = TextEditingController(text: category?['color'] ?? '#6366f1');
    String type = category?['type'] ?? 'expense';

    Get.bottomSheet(
      Container(
        padding: EdgeInsets.all(25),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(35))),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(category == null ? 'إضافة تصنيف جديد' : 'تعديل التصنيف', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
              SizedBox(height: 20),
              TextField(controller: nameController, decoration: InputDecoration(labelText: 'اسم التصنيف', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)))),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(child: TextField(controller: iconController, decoration: InputDecoration(labelText: 'الأيقونة (Emoji)', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))))),
                  SizedBox(width: 15),
                  Expanded(child: TextField(controller: colorController, decoration: InputDecoration(labelText: 'اللون (Hex)', border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))))),
                ],
              ),
              SizedBox(height: 15),
              DropdownButtonFormField<String>(
                value: type,
                decoration: InputDecoration(border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))),
                items: [
                  DropdownMenuItem(value: 'expense', child: Text('مصاريف (صادر)')),
                  DropdownMenuItem(value: 'income', child: Text('دخل (وارد)')),
                  DropdownMenuItem(value: 'capital', child: Text('رأس مال (صناديق فقط)')),
                ],
                onChanged: (val) => type = val!,
              ),
              SizedBox(height: 30),
              ElevatedButton(
                onPressed: () async {
                  final data = {
                    'name': nameController.text,
                    'icon': iconController.text,
                    'color': colorController.text,
                    'type': type,
                  };
                  bool success;
                  if (category == null) {
                    success = await apiService.addCategory(data);
                  } else {
                    success = await apiService.updateCategory(category['id'], data);
                  }
                  if (success) {
                    Get.back();
                    loadCategories();
                    Get.snackbar('تم', 'تم حفظ التصنيف بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(backgroundColor: Colors.indigo, minimumSize: Size(double.infinity, 60), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
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
        title: Text('التصنيفات', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : ListView.builder(
            padding: EdgeInsets.all(20),
            itemCount: categories.length,
            itemBuilder: (context, i) {
              final c = categories[i];
              return Container(
                margin: EdgeInsets.only(bottom: 15),
                padding: EdgeInsets.all(15),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  border: Border.all(color: Colors.indigo.shade50, width: 2),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 50, height: 50,
                      decoration: BoxDecoration(
                        color: Color(int.parse(c['color'].replaceFirst('#', '0xFF'))).withOpacity(0.1),
                        borderRadius: BorderRadius.circular(15)
                      ),
                      child: Center(child: Text(c['icon'] ?? '📁', style: TextStyle(fontSize: 24))),
                    ),
                    SizedBox(width: 15),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(c['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Colors.indigo.shade900)),
                          Text(
                            c['type'] == 'income' ? 'دخل' : (c['type'] == 'capital' ? 'رأس مال' : 'مصاريف'), 
                            style: TextStyle(
                              color: c['type'] == 'income' ? Colors.green : (c['type'] == 'capital' ? Colors.amber.shade700 : Colors.red), 
                              fontWeight: FontWeight.bold, fontSize: 12
                            )
                          ),
                        ],
                      ),
                    ),
                    if (c['user_id'] != null) ...[
                      IconButton(icon: Icon(Icons.edit, color: Colors.indigo.shade300), onPressed: () => _showCategoryDialog(category: c)),
                      IconButton(icon: Icon(Icons.delete, color: Colors.red.shade300), onPressed: () async {
                        if (await apiService.deleteCategory(c['id'])) loadCategories();
                      }),
                    ] else 
                      Text('افتراضي', style: TextStyle(color: Colors.grey.shade400, fontSize: 10, fontWeight: FontWeight.bold)),
                  ],
                ),
              );
            },
          ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showCategoryDialog(),
        backgroundColor: Colors.indigo,
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
