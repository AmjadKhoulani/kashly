import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';

class DebtsScreen extends StatefulWidget {
  @override
  _DebtsScreenState createState() => _DebtsScreenState();
}

class _DebtsScreenState extends State<DebtsScreen> {
  final apiService = ApiService();
  bool isLoading = true;
  List entries = [];
  double totalReceivablesUsd = 0;
  double totalPayablesUsd = 0;
  double netDebtsUsd = 0;

  @override
  void initState() {
    super.initState();
    loadLedger();
  }

  void loadLedger() async {
    final result = await apiService.getLedger();
    setState(() {
      if (result != null) {
        entries = result['entries'] ?? [];
        totalReceivablesUsd = double.tryParse(result['total_receivables_usd']?.toString() ?? '0') ?? 0.0;
        totalPayablesUsd = double.tryParse(result['total_payables_usd']?.toString() ?? '0') ?? 0.0;
        netDebtsUsd = double.tryParse(result['net_debts_usd']?.toString() ?? '0') ?? 0.0;
      }
      isLoading = false;
    });
  }

  void _showAddDebtDialog() {
    final partyNameController = TextEditingController();
    final phoneController = TextEditingController();
    final amountController = TextEditingController();
    final descController = TextEditingController();
    final noteController = TextEditingController();
    
    String type = 'receivable';
    String currency = 'USD';
    DateTime selectedDueDate = DateTime.now().add(Duration(days: 30));

    Get.bottomSheet(
      StatefulBuilder(
        builder: (context, setModalState) {
          Future<void> _pickDate() async {
            final DateTime? picked = await showDatePicker(
              context: context,
              initialDate: selectedDueDate,
              firstDate: DateTime.now().subtract(Duration(days: 365)),
              lastDate: DateTime.now().add(Duration(days: 3650)),
              builder: (context, child) {
                return Theme(
                  data: Theme.of(context).copyWith(
                    colorScheme: ColorScheme.light(
                      primary: Color(0xFF4F46E5),
                      onPrimary: Colors.white,
                      onSurface: Color(0xFF0F172A),
                    ),
                    textTheme: GoogleFonts.almaraiTextTheme(Theme.of(context).textTheme),
                  ),
                  child: child!,
                );
              },
            );
            if (picked != null) {
              setModalState(() => selectedDueDate = picked);
            }
          }

          return Container(
            padding: EdgeInsets.only(left: 24, right: 24, top: 25, bottom: 40),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(35)),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.15), blurRadius: 20, spreadRadius: 5)],
            ),
            child: SingleChildScrollView(
              physics: BouncingScrollPhysics(),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 50, height: 5,
                      decoration: BoxDecoration(color: Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  SizedBox(height: 20),
                  Text(
                    'تسجيل دين / ذمة مالية جديدة',
                    textAlign: TextAlign.center,
                    style: GoogleFonts.almarai(fontSize: 17, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                  ),
                  SizedBox(height: 25),

                  // Debtor/Creditor name
                  TextField(
                    controller: partyNameController,
                    style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                    decoration: InputDecoration(
                      prefixIcon: Icon(Icons.person_outline_rounded, color: Color(0xFF4F46E5)),
                      labelText: 'اسم الطرف الآخر (العميل / الدائن)',
                      labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                      filled: true,
                      fillColor: Color(0xFFF8FAFC),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                    ),
                  ),
                  SizedBox(height: 15),

                  // Phone and type
                  Row(
                    children: [
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: type,
                          dropdownColor: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                          decoration: InputDecoration(
                            labelText: 'نوع الذمة',
                            labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 10, fontWeight: FontWeight.bold),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                          ),
                          items: [
                            DropdownMenuItem(value: 'receivable', child: Text('ديون لي (وارد)')),
                            DropdownMenuItem(value: 'payable', child: Text('ديون عليّ (صادر)')),
                            DropdownMenuItem(value: 'loan', child: Text('قرض شخصي')),
                            DropdownMenuItem(value: 'installment', child: Text('قسط مستحق')),
                          ],
                          onChanged: (val) => setModalState(() => type = val!),
                        ),
                      ),
                      SizedBox(width: 15),
                      Expanded(
                        child: TextField(
                          controller: phoneController,
                          keyboardType: TextInputType.phone,
                          style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                          decoration: InputDecoration(
                            prefixIcon: Icon(Icons.phone_outlined, color: Color(0xFF4F46E5), size: 18),
                            labelText: 'رقم الهاتف (اختياري)',
                            labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 10, fontWeight: FontWeight.bold),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                          ),
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 15),

                  // Amount and Currency
                  Row(
                    children: [
                      Expanded(
                        flex: 2,
                        child: TextField(
                          controller: amountController,
                          keyboardType: TextInputType.numberWithOptions(decimal: true),
                          style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                          decoration: InputDecoration(
                            prefixIcon: Icon(Icons.monetization_on_outlined, color: Color(0xFF4F46E5)),
                            labelText: 'المبلغ الإجمالي للدين',
                            labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                          ),
                        ),
                      ),
                      SizedBox(width: 15),
                      Expanded(
                        flex: 1,
                        child: DropdownButtonFormField<String>(
                          value: currency,
                          dropdownColor: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                          decoration: InputDecoration(
                            labelText: 'العملة',
                            labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 10, fontWeight: FontWeight.bold),
                            filled: true,
                            fillColor: Color(0xFFF8FAFC),
                            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                          ),
                          items: [
                            DropdownMenuItem(value: 'USD', child: Text('USD')),
                            DropdownMenuItem(value: 'SYP', child: Text('SYP')),
                            DropdownMenuItem(value: 'EUR', child: Text('EUR')),
                          ],
                          onChanged: (val) => setModalState(() => currency = val!),
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 15),

                  // Due Date card selector
                  GestureDetector(
                    onTap: _pickDate,
                    child: Container(
                      padding: EdgeInsets.symmetric(horizontal: 20, vertical: 15),
                      decoration: BoxDecoration(
                        color: Color(0xFFF8FAFC),
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(color: Color(0xFFE2E8F0)),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.date_range_rounded, color: Color(0xFF4F46E5), size: 20),
                          SizedBox(width: 15),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('تاريخ السداد / الاستحقاق', style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF64748B), fontWeight: FontWeight.bold)),
                              SizedBox(height: 2),
                              Text(DateFormat('yyyy-MM-dd').format(selectedDueDate), style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF1E293B), fontSize: 12)),
                            ],
                          ),
                          Spacer(),
                          Icon(Icons.edit_calendar_rounded, color: Color(0xFF64748B), size: 16),
                        ],
                      ),
                    ),
                  ),
                  SizedBox(height: 15),

                  // Description
                  TextField(
                    controller: descController,
                    style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                    decoration: InputDecoration(
                      prefixIcon: Icon(Icons.notes_rounded, color: Color(0xFF4F46E5)),
                      labelText: 'تفاصيل / سبب الدين',
                      labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                      filled: true,
                      fillColor: Color(0xFFF8FAFC),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                    ),
                  ),
                  SizedBox(height: 30),

                  // Save button
                  ElevatedButton(
                    onPressed: () async {
                      if (partyNameController.text.isEmpty || amountController.text.isEmpty) {
                        Get.snackbar('حقول ناقصة', 'يرجى إدخال اسم الطرف والمبلغ إجمالاً');
                        return;
                      }
                      final data = {
                        'type': type,
                        'party_name': partyNameController.text,
                        'party_phone': phoneController.text,
                        'total_amount': amountController.text,
                        'currency': currency,
                        'due_date': DateFormat('yyyy-MM-dd').format(selectedDueDate),
                        'description': descController.text,
                        'notes': noteController.text,
                      };
                      Get.back();
                      setState(() => isLoading = true);
                      final success = await apiService.addLedger(data);
                      if (success) {
                        loadLedger();
                        Get.snackbar('تم بنجاح', 'تم تسجيل القيد في دفتر الديون', backgroundColor: Color(0xFF10B981).withOpacity(0.9), colorText: Colors.white);
                      } else {
                        setState(() => isLoading = false);
                        Get.snackbar('فشل التسجيل', 'حدث خطأ غير متوقع', backgroundColor: Colors.redAccent.withOpacity(0.9), colorText: Colors.white);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Color(0xFF4F46E5),
                      foregroundColor: Colors.white,
                      minimumSize: Size(double.infinity, 58),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
                    ),
                    child: Text('حفظ بيانات الدين الجديد', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14)),
                  ),
                ],
              ),
            ),
          );
        }
      ),
      isScrollControlled: true,
    );
  }

  void _showAddPaymentDialog(Map entry) {
    final amountController = TextEditingController(text: entry['remaining_amount'].toString());
    final noteController = TextEditingController();
    DateTime payDate = DateTime.now();

    Get.bottomSheet(
      StatefulBuilder(
        builder: (context, setModalState) {
          return Container(
            padding: EdgeInsets.only(left: 24, right: 24, top: 25, bottom: 40),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(35)),
            ),
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 50, height: 5,
                      decoration: BoxDecoration(color: Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  SizedBox(height: 20),
                  Text(
                    'تسجيل دفعة سداد / استلام مالي',
                    textAlign: TextAlign.center,
                    style: GoogleFonts.almarai(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'الطرف الآخر: ${entry['party_name']} | المتبقي: ${entry['remaining_amount']} ${entry['currency']}',
                    textAlign: TextAlign.center,
                    style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF64748B), fontWeight: FontWeight.bold),
                  ),
                  SizedBox(height: 25),

                  // Amount
                  TextField(
                    controller: amountController,
                    keyboardType: TextInputType.numberWithOptions(decimal: true),
                    style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                    decoration: InputDecoration(
                      prefixIcon: Icon(Icons.payment_rounded, color: Color(0xFF4F46E5)),
                      labelText: 'المبلغ المدفوع حالياً (${entry['currency']})',
                      labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                      filled: true,
                      fillColor: Color(0xFFF8FAFC),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                    ),
                  ),
                  SizedBox(height: 15),

                  // Notes
                  TextField(
                    controller: noteController,
                    style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
                    decoration: InputDecoration(
                      prefixIcon: Icon(Icons.edit_note_rounded, color: Color(0xFF4F46E5)),
                      labelText: 'ملاحظات الدفعة (مثال: حوالة مصرفية، نقدي)',
                      labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
                      filled: true,
                      fillColor: Color(0xFFF8FAFC),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(18), borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5)),
                    ),
                  ),
                  SizedBox(height: 30),

                  // Save button
                  ElevatedButton(
                    onPressed: () async {
                      if (amountController.text.isEmpty) {
                        Get.snackbar('حقول ناقصة', 'يرجى إدخال مبلغ الدفعة');
                        return;
                      }
                      final data = {
                        'payment_date': DateFormat('yyyy-MM-dd').format(payDate),
                        'amount': amountController.text,
                        'notes': noteController.text,
                      };
                      Get.back();
                      setState(() => isLoading = true);
                      final success = await apiService.addLedgerPayment(entry['id'], data);
                      if (success) {
                        loadLedger();
                        Get.snackbar('تم السداد بنجاح', 'تم تسجيل الدفعة وتحديث الرصيد المتبقي للدين', backgroundColor: Color(0xFF10B981).withOpacity(0.9), colorText: Colors.white);
                      } else {
                        setState(() => isLoading = false);
                        Get.snackbar('فشل التسجيل', 'حدث خطأ أثناء إجراء الدفعة', backgroundColor: Colors.redAccent.withOpacity(0.9), colorText: Colors.white);
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Color(0xFF10B981),
                      foregroundColor: Colors.white,
                      minimumSize: Size(double.infinity, 58),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
                    ),
                    child: Text('تأكيد دفعة السداد', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14)),
                  ),
                ],
              ),
            ),
          );
        }
      ),
    );
  }

  void _confirmDelete(Map entry) {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: Text('حذف قيد الديون', textAlign: TextAlign.center, style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15)),
        content: Text('هل أنت متأكد من حذف هذا القيد للطرف "${entry['party_name']}" نهائياً؟ سيتم محو كافة الحركات والمدفوعات المسجلة عليه.', textAlign: TextAlign.center, style: GoogleFonts.almarai(fontSize: 12, height: 1.6)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف نهائي', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              setState(() => isLoading = true);
              final success = await apiService.deleteLedger(entry['id']);
              if (success) {
                loadLedger();
                Get.snackbar('تم الحذف', 'تم حذف القيد بنجاح من الدفتر');
              } else {
                setState(() => isLoading = false);
                Get.snackbar('فشل الحذف', 'حدث خطأ أثناء العملية');
              }
            },
          ),
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat('#,##0.00');
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('دفتر الديون والالتزامات', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Color(0xFF4F46E5)))
          : RefreshIndicator(
              onRefresh: () async => loadLedger(),
              child: SingleChildScrollView(
                physics: BouncingScrollPhysics(),
                padding: EdgeInsets.symmetric(horizontal: 24, vertical: 15),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Unified Summary Dashboard Card
                    _buildSummaryCard(currencyFormat),
                    SizedBox(height: 25),

                    // Ledger entries list title
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('الديون النشطة والالتزامات', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF64748B))),
                        Text('${entries.length} قيود مسجلة', style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                      ],
                    ),
                    SizedBox(height: 15),

                    // Entries list
                    entries.isEmpty ? _buildEmptyState() : _buildEntriesList(currencyFormat),
                  ],
                ),
              ),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: _showAddDebtDialog,
        backgroundColor: Color(0xFF4F46E5),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        elevation: 5,
        child: Icon(Icons.add_rounded, color: Colors.white, size: 28),
      ),
    );
  }

  Widget _buildSummaryCard(NumberFormat format) {
    final isNegative = netDebtsUsd < 0;
    return Container(
      padding: EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E1B4B)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [BoxShadow(color: Color(0xFF1E1B4B).withOpacity(0.15), blurRadius: 20, offset: Offset(0, 10))],
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('صافي الرصيد المستحق (بالدولار)', style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontSize: 10, fontWeight: FontWeight.bold)),
                  SizedBox(height: 4),
                  Text(
                    '${isNegative ? '-' : '+'}\$${format.format(netDebtsUsd.abs())}',
                    style: GoogleFonts.almarai(color: isNegative ? Color(0xFFFCA5A5) : Color(0xFF34D399), fontSize: 26, fontWeight: FontWeight.w900),
                  ),
                ],
              ),
              Container(
                padding: EdgeInsets.all(12),
                decoration: BoxDecoration(color: Colors.white.withOpacity(0.06), shape: BoxShape.circle),
                child: Icon(Icons.balance_rounded, color: Colors.white, size: 24),
              )
            ],
          ),
          SizedBox(height: 20),
          Divider(color: Colors.white.withOpacity(0.1), height: 1),
          SizedBox(height: 18),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('💸 ديون مستحقة لي', style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontSize: 9, fontWeight: FontWeight.bold)),
                    SizedBox(height: 3),
                    Text('\$${format.format(totalReceivablesUsd)}', style: GoogleFonts.almarai(color: Color(0xFF34D399), fontSize: 14, fontWeight: FontWeight.w900)),
                  ],
                ),
              ),
              Container(
                width: 1,
                height: 30,
                color: Colors.white.withOpacity(0.1),
                margin: EdgeInsets.symmetric(horizontal: 15),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('📈 ديون مستحقة عليّ', style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontSize: 9, fontWeight: FontWeight.bold)),
                    SizedBox(height: 3),
                    Text('\$${format.format(totalPayablesUsd)}', style: GoogleFonts.almarai(color: Color(0xFFFCA5A5), fontSize: 14, fontWeight: FontWeight.w900)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEntriesList(NumberFormat format) {
    return ListView.builder(
      shrinkWrap: true,
      physics: NeverScrollableScrollPhysics(),
      itemCount: entries.length,
      itemBuilder: (context, i) {
        final e = entries[i];
        final total = double.tryParse(e['total_amount']?.toString() ?? '0') ?? 0.0;
        final paid = double.tryParse(e['paid_amount']?.toString() ?? '0') ?? 0.0;
        final remaining = double.tryParse(e['remaining_amount']?.toString() ?? '0') ?? 0.0;
        
        final double progress = total > 0 ? (paid / total).clamp(0.0, 1.0) : 0.0;
        final String currency = e['currency'] ?? 'USD';

        // Colored status pill properties
        Color typeColor = Colors.indigo;
        Color typeBg = Colors.indigo.shade50;
        if (e['type'] == 'receivable') {
          typeColor = Color(0xFF059669);
          typeBg = Color(0xFFECFDF5);
        } else if (e['type'] == 'payable') {
          typeColor = Color(0xFFDC2626);
          typeBg = Color(0xFFFEF2F2);
        } else if (e['type'] == 'loan') {
          typeColor = Color(0xFFD97706);
          typeBg = Color(0xFFFEF3C7);
        }

        final bool isSettled = e['status'] == 'settled';

        return Container(
          margin: EdgeInsets.only(bottom: 15),
          padding: EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(26),
            border: Border.all(color: Color(0xFFE2E8F0)),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 10, offset: Offset(0, 4))],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Top detail row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(color: typeBg, borderRadius: BorderRadius.circular(8)),
                    child: Text(e['type_label'] ?? 'قيد دين', style: GoogleFonts.almarai(color: typeColor, fontWeight: FontWeight.w900, fontSize: 9)),
                  ),
                  
                  // Days left / status indicator
                  if (isSettled)
                    Container(
                      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(color: Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6)),
                      child: Text('تم السداد بالكامل ✓', style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.w900, fontSize: 9)),
                    )
                  else if (e['days_left'] != null) ...[
                    if (e['days_left'] < 0)
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(color: Color(0xFFFEE2E2), borderRadius: BorderRadius.circular(6)),
                        child: Text('متأخر ${e['days_left'].abs()} يوم', style: GoogleFonts.almarai(color: Color(0xFFDC2626), fontWeight: FontWeight.w900, fontSize: 9)),
                      )
                    else if (e['days_left'] == 0)
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(color: Color(0xFFFEF3C7), borderRadius: BorderRadius.circular(6)),
                        child: Text('يستحق اليوم ⚠️', style: GoogleFonts.almarai(color: Color(0xFFD97706), fontWeight: FontWeight.w900, fontSize: 9)),
                      )
                    else
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(color: Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6)),
                        child: Text('متبقي ${e['days_left']} يوم', style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 9)),
                      )
                  ],
                ],
              ),
              SizedBox(height: 12),

              // Debtor / Creditor name
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(e['party_name'] ?? 'بدون اسم', style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                        if (e['description'] != null && e['description'].toString().isNotEmpty) ...[
                          SizedBox(height: 2),
                          Text(e['description'].toString(), style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                        ],
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text('المبلغ المتبقي', style: GoogleFonts.almarai(fontSize: 9, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                      Text('${format.format(remaining)} $currency', style: GoogleFonts.almarai(fontSize: 15, fontWeight: FontWeight.w900, color: typeColor)),
                    ],
                  ),
                ],
              ),
              SizedBox(height: 15),

              // Repayment progress bar
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('تم سداد: ${format.format(paid)} $currency', style: GoogleFonts.almarai(fontSize: 9, color: Color(0xFF64748B), fontWeight: FontWeight.bold)),
                  Text('${(progress * 100).toStringAsFixed(0)}%', style: GoogleFonts.almarai(fontSize: 9, color: typeColor, fontWeight: FontWeight.w900)),
                ],
              ),
              SizedBox(height: 5),
              ClipRRect(
                borderRadius: BorderRadius.circular(5),
                child: Container(
                  height: 6,
                  color: Color(0xFFF1F5F9),
                  child: Align(
                    alignment: Alignment.centerRight,
                    child: Container(
                      width: double.infinity,
                      child: FractionallySizedBox(
                        alignment: Alignment.centerRight,
                        widthFactor: progress,
                        child: Container(color: typeColor),
                      ),
                    ),
                  ),
                ),
              ),

              // Action buttons below progress bar
              if (!isSettled) ...[
                SizedBox(height: 18),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => _showAddPaymentDialog(e),
                        style: OutlinedButton.styleFrom(
                          padding: EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          side: BorderSide(color: Color(0xFF10B981)),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.payment_rounded, color: Color(0xFF10B981), size: 16),
                            SizedBox(width: 8),
                            Text('تسجيل دفعة سداد', style: GoogleFonts.almarai(color: Color(0xFF10B981), fontSize: 11, fontWeight: FontWeight.w900)),
                          ],
                        ),
                      ),
                    ),
                    SizedBox(width: 12),
                    IconButton(
                      icon: Icon(Icons.delete_outline_rounded, color: Colors.red.shade300, size: 20),
                      onPressed: () => _confirmDelete(e),
                      tooltip: 'حذف القيد',
                    ),
                  ],
                ),
              ] else ...[
                SizedBox(height: 10),
                Align(
                  alignment: Alignment.centerLeft,
                  child: TextButton(
                    onPressed: () => _confirmDelete(e),
                    child: Text('حذف السجل التاريخي', style: GoogleFonts.almarai(color: Colors.red.shade400, fontSize: 10, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          SizedBox(height: 30),
          Container(
            padding: EdgeInsets.all(24),
            decoration: BoxDecoration(color: Color(0xFFEEF2FF), shape: BoxShape.circle),
            child: Icon(Icons.balance_rounded, size: 55, color: Color(0xFF4F46E5)),
          ),
          SizedBox(height: 20),
          Text('لا توجد ذمم أو ديون حالياً', style: GoogleFonts.almarai(fontSize: 15, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
          SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 40),
            child: Text(
              'دفترك المالي خالٍ تماماً. يمكنك البدء في تسجيل الديون والالتزامات بذمة الآخرين لمتابعة مواعيد استحقاقها بسهولة.',
              textAlign: TextAlign.center,
              style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF64748B), height: 1.5, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }
}
