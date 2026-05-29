import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';

class TransferScreen extends StatefulWidget {
  final int sourceId;
  final String sourceType;
  final List paymentMethods;

  TransferScreen({
    required this.sourceId,
    required this.sourceType,
    required this.paymentMethods,
  });

  @override
  _TransferScreenState createState() => _TransferScreenState();
}

class _TransferScreenState extends State<TransferScreen> {
  final apiService = ApiService();
  final amountController = TextEditingController();
  final descController = TextEditingController();
  
  int? fromAccountId;
  int? toAccountId;
  DateTime selectedDate = DateTime.now();
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    // Default fromAccountId to sourceId if it matches
    if (widget.paymentMethods.any((pm) => pm['id'] == widget.sourceId)) {
      fromAccountId = widget.sourceId;
    }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: Color(0xFF4F46E5),
              onPrimary: Colors.white,
              onSurface: Color(0xFF1E1B4B),
            ),
            textTheme: GoogleFonts.almaraiTextTheme(Theme.of(context).textTheme),
          ),
          child: child!,
        );
      },
    );
    if (picked != null && picked != selectedDate) {
      setState(() => selectedDate = picked);
    }
  }

  void save() async {
    if (amountController.text.isEmpty || fromAccountId == null || toAccountId == null) {
      Get.snackbar(
        'خطأ في التحويل',
        'يرجى ملء كافة الحقول وتحديد الحسابات',
        backgroundColor: Colors.redAccent.withOpacity(0.9),
        colorText: Colors.white,
        borderRadius: 15,
        margin: EdgeInsets.all(15),
      );
      return;
    }

    if (fromAccountId == toAccountId) {
      Get.snackbar(
        'خطأ في الحسابات',
        'لا يمكن التحويل لنفس الحساب. يرجى اختيار حسابين مختلفين',
        backgroundColor: Colors.amber.shade800.withOpacity(0.9),
        colorText: Colors.white,
        borderRadius: 15,
        margin: EdgeInsets.all(15),
      );
      return;
    }

    setState(() => isSaving = true);

    final data = {
      'amount': amountController.text,
      'from_payment_method_id': fromAccountId,
      'to_payment_method_id': toAccountId,
      'transaction_date': DateFormat('yyyy-MM-dd').format(selectedDate),
      'description': descController.text,
      'source_type': widget.sourceType,
      'source_id': widget.sourceId,
    };

    final success = await apiService.transfer(data);
    setState(() => isSaving = false);

    if (success) {
      Get.back(result: true);
      Get.snackbar(
        'تم بنجاح',
        'تمت عملية التحويل المالي وتحديث الأرصدة بنجاح',
        backgroundColor: Color(0xFF10B981).withOpacity(0.9),
        colorText: Colors.white,
        borderRadius: 15,
        margin: EdgeInsets.all(15),
      );
    } else {
      Get.snackbar(
        'فشل التحويل',
        'حدث خطأ أثناء إجراء العملية، يرجى التحقق من الرصيد المتوفر',
        backgroundColor: Colors.redAccent.withOpacity(0.9),
        colorText: Colors.white,
        borderRadius: 15,
        margin: EdgeInsets.all(15),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'تحويل بين الحسابات',
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
      body: GestureDetector(
        onTap: () => FocusScope.of(context).unfocus(),
        child: SingleChildScrollView(
          physics: BouncingScrollPhysics(),
          padding: EdgeInsets.symmetric(horizontal: 24, vertical: 15),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header Card Info
              Container(
                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 15),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF4F46E5), Color(0xFF6366F1)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                      color: Color(0xFF4F46E5).withOpacity(0.2),
                      blurRadius: 15,
                      offset: Offset(0, 8),
                    )
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.swap_horiz_rounded, color: Colors.white, size: 28),
                    ),
                    SizedBox(width: 15),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'تسوية الأرصدة الذكية',
                            style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 14),
                          ),
                          SizedBox(height: 3),
                          Text(
                            'حول الأموال بين محافظك وحساباتك بنقرة واحدة مع توثيق فوري.',
                            style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.8), fontSize: 10),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: 25),

              // Flow Selection Area
              Container(
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(30),
                  border: Border.all(color: Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.015),
                      blurRadius: 15,
                      offset: Offset(0, 10),
                    )
                  ],
                ),
                child: Column(
                  children: [
                    _buildAccountDropdown('من حساب (مصدر التحويل)', fromAccountId, (val) => setState(() => fromAccountId = val), true),
                    
                    // Arrow transfer indicator
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      child: Row(
                        children: [
                          Expanded(child: Divider(color: Color(0xFFE2E8F0), thickness: 1)),
                          Container(
                            padding: EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Color(0xFFEEF2FF),
                              shape: BoxShape.circle,
                              border: Border.all(color: Color(0xFFE0E7FF), width: 1),
                            ),
                            child: Icon(Icons.arrow_downward_rounded, color: Color(0xFF4F46E5), size: 18),
                          ),
                          Expanded(child: Divider(color: Color(0xFFE2E8F0), thickness: 1)),
                        ],
                      ),
                    ),

                    _buildAccountDropdown('إلى حساب (جهة التحويل)', toAccountId, (val) => setState(() => toAccountId = val), false),
                  ],
                ),
              ),
              SizedBox(height: 20),

              // Inputs Area
              Container(
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(30),
                  border: Border.all(color: Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.015),
                      blurRadius: 15,
                      offset: Offset(0, 10),
                    )
                  ],
                ),
                child: Column(
                  children: [
                    _buildTextField(
                      amountController,
                      'المبلغ المراد تحويله',
                      Icons.attach_money_rounded,
                      TextInputType.numberWithOptions(decimal: true),
                      isAmount: true,
                    ),
                    SizedBox(height: 18),
                    _buildDatePicker(),
                    SizedBox(height: 18),
                    _buildTextField(
                      descController,
                      'ملاحظات عملية التحويل (اختياري)',
                      Icons.description_outlined,
                      TextInputType.text,
                    ),
                  ],
                ),
              ),
              SizedBox(height: 35),

              // Action Button
              ElevatedButton(
                onPressed: isSaving ? null : save,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Color(0xFF4F46E5),
                  foregroundColor: Colors.white,
                  disabledBackgroundColor: Color(0xFF4F46E5).withOpacity(0.6),
                  padding: EdgeInsets.symmetric(vertical: 20),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
                  elevation: 4,
                  shadowColor: Color(0xFF4F46E5).withOpacity(0.2),
                ),
                child: isSaving
                    ? SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                      )
                    : Text(
                        'تأكيد عملية التحويل',
                        style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15),
                      ),
              )
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDatePicker() {
    return GestureDetector(
      onTap: () => _selectDate(context),
      child: Container(
        padding: EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        decoration: BoxDecoration(
          color: Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Color(0xFFE2E8F0)),
        ),
        child: Row(
          children: [
            Icon(Icons.calendar_today_rounded, color: Color(0xFF4F46E5), size: 18),
            SizedBox(width: 15),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'تاريخ العملية',
                  style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF64748B), fontWeight: FontWeight.bold),
                ),
                SizedBox(height: 3),
                Text(
                  DateFormat('yyyy-MM-dd').format(selectedDate),
                  style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF1E293B), fontSize: 13),
                ),
              ],
            ),
            Spacer(),
            Icon(Icons.edit_calendar_rounded, color: Color(0xFF64748B), size: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountDropdown(String label, int? value, Function(int?) onChanged, bool isSource) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      decoration: BoxDecoration(
        color: Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 6,
                height: 6,
                decoration: BoxDecoration(
                  color: isSource ? Color(0xFF4F46E5) : Color(0xFFF59E0B),
                  shape: BoxShape.circle,
                ),
              ),
              SizedBox(width: 8),
              Text(
                label,
                style: GoogleFonts.almarai(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF64748B)),
              ),
            ],
          ),
          SizedBox(height: 4),
          DropdownButtonHideUnderline(
            child: DropdownButton<int>(
              hint: Text(
                'اختر الحساب المصرفي',
                style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 13, fontWeight: FontWeight.bold),
              ),
              value: value,
              isExpanded: true,
              icon: Icon(Icons.keyboard_arrow_down_rounded, color: Color(0xFF64748B)),
              style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
              dropdownColor: Colors.white,
              borderRadius: BorderRadius.circular(20),
              items: widget.paymentMethods.map<DropdownMenuItem<int>>((pm) {
                final balanceText = NumberFormat('#,##0').format(pm['balance']);
                final currencySymbol = pm['currency'] ?? 'SYP';
                return DropdownMenuItem<int>(
                  value: pm['id'],
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(pm['name'], style: GoogleFonts.almarai(fontWeight: FontWeight.w800)),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: isSource ? Color(0xFFEEF2FF) : Color(0xFFFEF3C7),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          '$balanceText $currencySymbol',
                          style: GoogleFonts.almarai(
                            color: isSource ? Color(0xFF4F46E5) : Color(0xFFD97706),
                            fontSize: 10,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                    ],
                  ),
                );
              }).toList(),
              onChanged: onChanged,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField(
    TextEditingController ctrl,
    String label,
    IconData icon,
    TextInputType ktype, {
    bool isAmount = false,
  }) {
    return TextField(
      controller: ctrl,
      keyboardType: ktype,
      style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
      decoration: InputDecoration(
        prefixIcon: Icon(icon, color: Color(0xFF4F46E5), size: 20),
        labelText: label,
        labelStyle: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 12, fontWeight: FontWeight.bold),
        filled: true,
        fillColor: Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(20),
          borderSide: BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(20),
          borderSide: BorderSide(color: Color(0xFF4F46E5), width: 1.5),
        ),
      ),
    );
  }
}
