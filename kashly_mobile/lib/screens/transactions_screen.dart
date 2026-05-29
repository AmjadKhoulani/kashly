import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';

class TransactionsScreen extends StatefulWidget {
  @override
  _TransactionsScreenState createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  final apiService = ApiService();
  List transactions = [];
  List categories = [];
  String? selectedType;
  String? selectedCategory;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadCategories();
    loadTransactions();
  }

  void loadCategories() async {
    final result = await apiService.getTransactionCategories();
    if (result != null) setState(() => categories = result);
  }

  void loadTransactions() async {
    setState(() => isLoading = true);
    final result = await apiService.getTransactions(type: selectedType, category: selectedCategory);
    setState(() {
      transactions = result?['data'] ?? [];
      isLoading = false;
    });
  }

  void _showTransactionOptions(Map t) {
    Get.bottomSheet(
      Container(
        padding: EdgeInsets.all(25),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(35)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Text(
                'خيارات العملية المالية',
                style: GoogleFonts.almarai(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
              ),
            ),
            SizedBox(height: 25),
            ListTile(
              leading: Container(
                padding: EdgeInsets.all(10),
                decoration: BoxDecoration(color: Colors.indigo.shade50, borderRadius: BorderRadius.circular(12)),
                child: Icon(Icons.edit_outlined, color: Colors.indigo),
              ),
              title: Text('تعديل تفاصيل العملية', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 14)),
              onTap: () {
                Get.back();
                _showEditTransactionDialog(t);
              },
            ),
            Divider(color: Color(0xFFF1F5F9)),
            ListTile(
              leading: Container(
                padding: EdgeInsets.all(10),
                decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(12)),
                child: Icon(Icons.delete_outline_rounded, color: Colors.red.shade700),
              ),
              title: Text('حذف العملية نهائياً', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.red.shade700)),
              onTap: () {
                Get.back();
                _confirmDeleteTransaction(t['id']);
              },
            ),
            SizedBox(height: 15),
          ],
        ),
      ),
    );
  }

  void _confirmDeleteTransaction(int id) {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف الحركة المالية', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.red.shade900)),
        content: Text('هل أنت متأكد من حذف هذه الحركة نهائياً؟ سيتم تسوية رصيد الحساب المرتبط بها تلقائياً وعكس أي أثر مالي لها.', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 13)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف العملية', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              final success = await apiService.deleteTransaction(id);
              if (success) {
                loadTransactions();
                Get.snackbar('تم الحذف', 'تم حذف العملية وتحديث الأرصدة بنجاح');
              }
            },
          ),
        ],
      )
    );
  }

  void _showEditTransactionDialog(Map t) {
    final descController = TextEditingController(text: t['description']);
    final amountController = TextEditingController(text: t['amount']?.toString());
    final dateController = TextEditingController(
      text: t['transaction_date'] != null
          ? DateFormat('yyyy-MM-dd').format(DateTime.parse(t['transaction_date']))
          : DateFormat('yyyy-MM-dd').format(DateTime.now()),
    );
    
    int? selectedCategoryId = t['category_id'] is int ? t['category_id'] : null;
    String type = t['type'] ?? 'expense';
    int? paymentMethodId = t['payment_method_id'] is int ? t['payment_method_id'] : null;

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
                  'تعديل العملية المالية',
                  style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.indigo.shade900),
                ),
              ),
              SizedBox(height: 25),
              TextField(
                controller: descController,
                decoration: InputDecoration(
                  labelText: 'توضيح الحركة (Description)',
                  labelStyle: GoogleFonts.almarai(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                ),
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: amountController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'المبلغ',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: TextField(
                      controller: dateController,
                      decoration: InputDecoration(
                        labelText: 'التاريخ',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      onTap: () async {
                        DateTime? picked = await showDatePicker(
                          context: context,
                          initialDate: DateTime.now(),
                          firstDate: DateTime(2020),
                          lastDate: DateTime(2030),
                        );
                        if (picked != null) {
                          dateController.text = DateFormat('yyyy-MM-dd').format(picked);
                        }
                      },
                    ),
                  ),
                ],
              ),
              SizedBox(height: 15),
              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: type,
                      decoration: InputDecoration(
                        labelText: 'النوع',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      items: [
                        DropdownMenuItem(value: 'expense', child: Text('مصروف', style: GoogleFonts.almarai(fontSize: 12))),
                        DropdownMenuItem(value: 'income', child: Text('دخل', style: GoogleFonts.almarai(fontSize: 12))),
                        DropdownMenuItem(value: 'capital', child: Text('رأس مال', style: GoogleFonts.almarai(fontSize: 12))),
                      ],
                      onChanged: (val) => type = val!,
                    ),
                  ),
                  SizedBox(width: 15),
                  Expanded(
                    child: DropdownButtonFormField<int>(
                      value: selectedCategoryId,
                      decoration: InputDecoration(
                        labelText: 'التصنيف',
                        labelStyle: GoogleFonts.almarai(fontSize: 12),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      items: categories.where((c) => c != null && c['id'] != null).map((c) {
                        return DropdownMenuItem<int>(
                          value: c['id'] as int,
                          child: Text(c['name'].toString(), style: GoogleFonts.almarai(fontSize: 12)),
                        );
                      }).toList(),
                      onChanged: (val) => selectedCategoryId = val,
                    ),
                  ),
                ],
              ),
              SizedBox(height: 30),
              ElevatedButton(
                onPressed: () async {
                  if (amountController.text.isEmpty || selectedCategoryId == null) {
                    Get.snackbar('خطأ', 'يرجى إدخال المبلغ وتحديد التصنيف');
                    return;
                  }
                  final data = {
                    'description': descController.text,
                    'amount': amountController.text,
                    'type': type,
                    'category_id': selectedCategoryId,
                    'transaction_date': dateController.text,
                    'payment_method_id': paymentMethodId,
                  };
                  final success = await apiService.updateTransaction(t['id'], data);
                  if (success) {
                    Get.back();
                    loadTransactions();
                    Get.snackbar('تم التعديل', 'تم تحديث تفاصيل العملية وتعديل الأرصدة بنجاح');
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.indigo,
                  minimumSize: Size(double.infinity, 56),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                child: Text(
                  'حفظ التعديلات',
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
    final format = NumberFormat('#,##0.00', 'en_US');

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          'سجل العمليات المالية',
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
      body: Column(
        children: [
          _buildFilters(),
          Expanded(
            child: isLoading
                ? Center(child: CircularProgressIndicator(color: Colors.indigo))
                : transactions.isEmpty
                    ? Center(
                        child: Text(
                          'لا توجد عمليات تطابق البحث الحركي',
                          style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold),
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: () async => loadTransactions(),
                        color: Colors.indigo,
                        child: ListView.builder(
                          physics: BouncingScrollPhysics(),
                          padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                          itemCount: transactions.length,
                          itemBuilder: (context, i) {
                            final t = transactions[i];
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
                            final String transactionDate = t['transaction_date'] != null
                                ? DateFormat('yyyy-MM-dd').format(DateTime.parse(t['transaction_date']))
                                : '';
                            final double amount = double.tryParse(t['amount'].toString()) ?? 0.0;
                            final String type = t['type'] ?? 'expense';

                            final String currency = (t['payment_method'] != null && t['payment_method']['currency'] != null)
                                ? t['payment_method']['currency'].toString()
                                : (t['currency']?.toString() ?? 'USD');

                            Color iconColor = Color(int.parse(categoryColor.replaceFirst('#', '0xFF')));
                            Color typeColor = type == 'income'
                                ? Colors.green.shade600
                                : (type == 'capital' ? Colors.indigo.shade600 : Colors.red.shade600);

                            return GestureDetector(
                              onLongPress: () => _showTransactionOptions(t),
                              child: Container(
                                margin: EdgeInsets.only(bottom: 12),
                                padding: EdgeInsets.all(16),
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
                                      width: 46,
                                      height: 46,
                                      decoration: BoxDecoration(
                                        color: iconColor.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(14),
                                      ),
                                      child: Center(child: Text(categoryIcon, style: TextStyle(fontSize: 20))),
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
                                          Text(
                                            '$categoryName • $transactionDate',
                                            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.bold),
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
                              ),
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilters() {
    return Container(
      height: 60,
      padding: EdgeInsets.symmetric(horizontal: 20),
      child: ListView(
        scrollDirection: Axis.horizontal,
        physics: BouncingScrollPhysics(),
        children: [
          _filterChip('الكل', null, 'type'),
          _filterChip('الإيداعات', 'income', 'type'),
          _filterChip('السحوبات', 'expense', 'type'),
          _filterChip('رؤوس الأموال', 'capital', 'type'),
          VerticalDivider(width: 30, indent: 15, endIndent: 15, color: Color(0xFFE2E8F0)),
          ...categories.where((c) => c != null && c['name'] != null).map((c) {
            return _filterChip(c['name'].toString(), c['name'].toString(), 'category');
          }).toList(),
        ],
      ),
    );
  }

  Widget _filterChip(String label, String? value, String filterType) {
    bool isSelected = filterType == 'type' ? selectedType == value : selectedCategory == value;
    return GestureDetector(
      onTap: () {
        setState(() {
          if (filterType == 'type') {
            selectedType = value;
          } else {
            selectedCategory = value;
          }
        });
        loadTransactions();
      },
      child: Container(
        margin: EdgeInsets.only(right: 8, top: 10, bottom: 10),
        padding: EdgeInsets.symmetric(horizontal: 18),
        decoration: BoxDecoration(
          color: isSelected ? Colors.indigo : Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: isSelected ? Colors.indigo : Color(0xFFE2E8F0), width: 1.5),
        ),
        child: Center(
          child: Text(
            label,
            style: GoogleFonts.almarai(
              color: isSelected ? Colors.white : Colors.indigo.shade700,
              fontWeight: FontWeight.w900,
              fontSize: 12,
            ),
          ),
        ),
      ),
    );
  }
}
