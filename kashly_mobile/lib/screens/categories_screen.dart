import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
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
        padding: EdgeInsets.only(left: 24, right: 24, top: 25, bottom: 40),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(35)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.15),
              blurRadius: 20,
              spreadRadius: 5,
            )
          ],
        ),
        child: SingleChildScrollView(
          physics: BouncingScrollPhysics(),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Pull Bar Indicator
              Center(
                child: Container(
                  width: 50,
                  height: 5,
                  decoration: BoxDecoration(
                    color: Color(0xFFE2E8F0),
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
              ),
              SizedBox(height: 20),
              
              Text(
                category == null ? 'إضافة تصنيف مالي جديد' : 'تعديل التصنيف المالي',
                textAlign: TextAlign.center,
                style: GoogleFonts.almarai(
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                  color: Color(0xFF0F172A),
                ),
              ),
              SizedBox(height: 25),

              // Name Field
              TextField(
                controller: nameController,
                style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                decoration: InputDecoration(
                  prefixIcon: Icon(Icons.edit_note_rounded, color: Color(0xFF4F46E5)),
                  labelText: 'اسم التصنيف',
                  labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                  filled: true,
                  fillColor: Color(0xFFF8FAFC),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(18),
                    borderSide: BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(18),
                    borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5),
                  ),
                ),
              ),
              SizedBox(height: 18),

              // Icon and Color Fields
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: iconController,
                      textAlign: TextAlign.center,
                      style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.bold),
                      decoration: InputDecoration(
                        labelText: 'الأيقونة (رمز تعبيري)',
                        labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 10, fontWeight: FontWeight.bold),
                        filled: true,
                        fillColor: Color(0xFFF8FAFC),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(18),
                          borderSide: BorderSide(color: Color(0xFFE2E8F0)),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(18),
                          borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5),
                        ),
                      ),
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: TextField(
                      controller: colorController,
                      style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 12, fontWeight: FontWeight.w800),
                      decoration: InputDecoration(
                        prefixIcon: Icon(Icons.palette_outlined, color: Color(0xFF4F46E5), size: 18),
                        labelText: 'اللون (Hex)',
                        labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 10, fontWeight: FontWeight.bold),
                        filled: true,
                        fillColor: Color(0xFFF8FAFC),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(18),
                          borderSide: BorderSide(color: Color(0xFFE2E8F0)),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(18),
                          borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 18),

              // Category Type Dropdown
              DropdownButtonFormField<String>(
                value: type,
                dropdownColor: Colors.white,
                borderRadius: BorderRadius.circular(20),
                style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                decoration: InputDecoration(
                  prefixIcon: Icon(Icons.layers_outlined, color: Color(0xFF4F46E5)),
                  labelText: 'نوع المعاملات التابعة',
                  labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                  filled: true,
                  fillColor: Color(0xFFF8FAFC),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(18),
                    borderSide: BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(18),
                    borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5),
                  ),
                ),
                items: [
                  DropdownMenuItem(value: 'expense', child: Text('مصاريف تشغيلية (صادر)')),
                  DropdownMenuItem(value: 'income', child: Text('إيرادات عامة (وارد)')),
                  DropdownMenuItem(value: 'capital', child: Text('رأس مال (حسابات وصناديق)')),
                ],
                onChanged: (val) => type = val!,
              ),
              SizedBox(height: 30),

              // Save button
              ElevatedButton(
                onPressed: () async {
                  if (nameController.text.isEmpty) {
                    Get.snackbar('خطأ', 'يرجى إدخال اسم التصنيف');
                    return;
                  }
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
                    Get.snackbar(
                      'تم بنجاح',
                      'تم حفظ بيانات التصنيف بنجاح وتحديث القائمة المتاحة',
                      backgroundColor: Color(0xFF10B981).withOpacity(0.9),
                      colorText: Colors.white,
                      borderRadius: 15,
                      margin: EdgeInsets.all(15),
                    );
                  } else {
                    Get.snackbar(
                      'فشل الحفظ',
                      'حدث خطأ غير متوقع أثناء حفظ البيانات، يرجى المحاولة لاحقاً',
                      backgroundColor: Colors.redAccent.withOpacity(0.9),
                      colorText: Colors.white,
                      borderRadius: 15,
                      margin: EdgeInsets.all(15),
                    );
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Color(0xFF4F46E5),
                  foregroundColor: Colors.white,
                  minimumSize: Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
                  elevation: 4,
                  shadowColor: Color(0xFF4F46E5).withOpacity(0.2),
                ),
                child: Text(
                  'حفظ بيانات التصنيف',
                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15),
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
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'إدارة التصنيفات المالية',
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: Color(0xFF0F172A), size: 20),
          onPressed: () => Get.back(),
        ),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Color(0xFF4F46E5)))
          : categories.isEmpty
              ? _buildEmptyState()
              : ListView.builder(
                  physics: BouncingScrollPhysics(),
                  padding: EdgeInsets.symmetric(horizontal: 24, vertical: 15),
                  itemCount: categories.length,
                  itemBuilder: (context, i) {
                    final c = categories[i];
                    Color catColor;
                    try {
                      catColor = Color(int.parse(c['color'].replaceFirst('#', '0xFF')));
                    } catch (e) {
                      catColor = Color(0xFF4F46E5);
                    }

                    // Format type name and style
                    String typeLabel = 'مصاريف';
                    Color typeColor = Colors.red;
                    Color typeBg = Colors.red.shade50;
                    if (c['type'] == 'income') {
                      typeLabel = 'دخل';
                      typeColor = Color(0xFF059669);
                      typeBg = Color(0xFFECFDF5);
                    } else if (c['type'] == 'capital') {
                      typeLabel = 'رأس مال';
                      typeColor = Color(0xFFD97706);
                      typeBg = Color(0xFFFEF3C7);
                    }

                    final bool isCustom = c['user_id'] != null;

                    return Container(
                      margin: EdgeInsets.only(bottom: 15),
                      padding: EdgeInsets.all(15),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(25),
                        border: Border.all(color: Color(0xFFE2E8F0)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.01),
                            blurRadius: 12,
                            offset: Offset(0, 4),
                          )
                        ],
                      ),
                      child: Row(
                        children: [
                          // Emoji Avatar Wrapper
                          Container(
                            width: 52,
                            height: 52,
                            decoration: BoxDecoration(
                              color: catColor.withOpacity(0.08),
                              borderRadius: BorderRadius.circular(18),
                              border: Border.all(color: catColor.withOpacity(0.15), width: 1.5),
                            ),
                            child: Center(
                              child: Text(
                                c['icon'] ?? '📁',
                                style: GoogleFonts.almarai(fontSize: 22),
                              ),
                            ),
                          ),
                          SizedBox(width: 15),

                          // Text Info
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  c['name'],
                                  style: GoogleFonts.almarai(
                                    fontWeight: FontWeight.w900,
                                    fontSize: 14,
                                    color: Color(0xFF0F172A),
                                  ),
                                ),
                                SizedBox(height: 4),
                                Row(
                                  children: [
                                    Container(
                                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                                      decoration: BoxDecoration(
                                        color: typeBg,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Text(
                                        typeLabel,
                                        style: GoogleFonts.almarai(
                                          color: typeColor,
                                          fontWeight: FontWeight.w900,
                                          fontSize: 10,
                                        ),
                                      ),
                                    ),
                                    if (!isCustom) ...[
                                      SizedBox(width: 8),
                                      Container(
                                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: Color(0xFFF1F5F9),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          'افتراضي',
                                          style: GoogleFonts.almarai(
                                            color: Color(0xFF64748B),
                                            fontSize: 9,
                                            fontWeight: FontWeight.w800,
                                          ),
                                        ),
                                      )
                                    ],
                                  ],
                                ),
                              ],
                            ),
                          ),

                          // Actions
                          if (isCustom) ...[
                            IconButton(
                              icon: Icon(Icons.mode_edit_outline_rounded, color: Color(0xFF4F46E5), size: 20),
                              onPressed: () => _showCategoryDialog(category: c),
                              tooltip: 'تعديل التصنيف',
                            ),
                            IconButton(
                              icon: Icon(Icons.delete_outline_rounded, color: Color(0xFFDC2626), size: 20),
                              onPressed: () => _confirmDelete(c),
                              tooltip: 'حذف التصنيف',
                            ),
                          ]
                        ],
                      ),
                    );
                  },
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showCategoryDialog(),
        backgroundColor: Color(0xFF4F46E5),
        elevation: 6,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        child: Icon(Icons.add_rounded, color: Colors.white, size: 28),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Color(0xFFEEF2FF),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.category_outlined, size: 64, color: Color(0xFF4F46E5)),
          ),
          SizedBox(height: 20),
          Text(
            'لا يوجد تصنيفات مخصصة',
            style: GoogleFonts.almarai(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
          ),
          SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 40),
            child: Text(
              'أضف أول تصنيف مالي خاص بك الآن للتحكم الكامل بنفقاتك ومصادر دخلك.',
              textAlign: TextAlign.center,
              style: GoogleFonts.almarai(fontSize: 12, color: Color(0xFF64748B), height: 1.5),
            ),
          ),
        ],
      ),
    );
  }

  void _confirmDelete(Map category) {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: Text(
          'تأكيد حذف التصنيف',
          textAlign: TextAlign.center,
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF0F172A)),
        ),
        content: Text(
          'هل أنت متأكد من حذف التصنيف "${category['name']}"؟\nسيؤدي هذا لإزالة التصنيف فقط، دون المساس بالعمليات التاريخية المقيدة عليه.',
          textAlign: TextAlign.center,
          style: GoogleFonts.almarai(fontSize: 12, height: 1.6, color: Color(0xFF64748B)),
        ),
        actionsPadding: EdgeInsets.only(left: 20, right: 20, bottom: 20),
        actionsAlignment: MainAxisAlignment.spaceEvenly,
        actions: [
          TextButton(
            style: TextButton.styleFrom(
              padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              backgroundColor: Color(0xFFF1F5F9),
            ),
            onPressed: () => Get.back(),
            child: Text(
              'إلغاء',
              style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.w800, fontSize: 12),
            ),
          ),
          TextButton(
            style: TextButton.styleFrom(
              padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              backgroundColor: Color(0xFFFEE2E2),
            ),
            onPressed: () async {
              Get.back();
              setState(() => isLoading = true);
              final success = await apiService.deleteCategory(category['id']);
              if (success) {
                loadCategories();
                Get.snackbar(
                  'تم الحذف',
                  'تمت إزالة التصنيف بنجاح من قائمتك المخصصة',
                  backgroundColor: Color(0xFFDC2626).withOpacity(0.9),
                  colorText: Colors.white,
                  borderRadius: 15,
                  margin: EdgeInsets.all(15),
                );
              } else {
                setState(() => isLoading = false);
                Get.snackbar(
                  'فشل الحذف',
                  'لا يمكن حذف هذا التصنيف لكونه قيد الاستخدام أو افتراضي',
                  backgroundColor: Colors.redAccent.withOpacity(0.9),
                  colorText: Colors.white,
                  borderRadius: 15,
                  margin: EdgeInsets.all(15),
                );
              }
            },
            child: Text(
              'تأكيد الحذف',
              style: GoogleFonts.almarai(color: Color(0xFFDC2626), fontWeight: FontWeight.w900, fontSize: 12),
            ),
          ),
        ],
      ),
    );
  }
}
