import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'package:intl/intl.dart';

class TransferScreen extends StatefulWidget {
  final int sourceId;
  final String sourceType;
  final List paymentMethods;

  TransferScreen({required this.sourceId, required this.sourceType, required this.paymentMethods});

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

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(primary: Colors.indigo, onPrimary: Colors.white, onSurface: Colors.indigo),
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
      Get.snackbar('خطأ', 'يرجى إكمال كافة الحقول');
      return;
    }

    if (fromAccountId == toAccountId) {
      Get.snackbar('خطأ', 'لا يمكن التحويل لنفس الحساب');
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
      Get.snackbar('تم', 'تم التحويل بنجاح');
    } else {
      Get.snackbar('خطأ', 'فشل في عملية التحويل');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: Text('تحويل بين الحسابات', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _buildAccountDropdown('من حساب', fromAccountId, (val) => setState(() => fromAccountId = val)),
            SizedBox(height: 15),
            Center(
              child: Container(
                padding: EdgeInsets.all(10),
                decoration: BoxDecoration(color: Colors.indigo.withOpacity(0.1), shape: BoxShape.circle),
                child: Icon(Icons.arrow_downward, color: Colors.indigo),
              ),
            ),
            SizedBox(height: 15),
            _buildAccountDropdown('إلى حساب', toAccountId, (val) => setState(() => toAccountId = val)),
            SizedBox(height: 25),
            _buildTextField(amountController, 'المبلغ المراد تحويله', Icons.attach_money, TextInputType.number),
            SizedBox(height: 20),
            _buildDatePicker(),
            SizedBox(height: 20),
            _buildTextField(descController, 'ملاحظات التحويل (اختياري)', Icons.note, TextInputType.text),
            SizedBox(height: 40),
            ElevatedButton(
              onPressed: isSaving ? null : save,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.amber.shade600,
                padding: EdgeInsets.symmetric(vertical: 20),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                elevation: 5,
                shadowColor: Colors.amber.withOpacity(0.4),
              ),
              child: isSaving 
                ? CircularProgressIndicator(color: Colors.white)
                : Text('تأكيد عملية التحويل', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildDatePicker() {
    return GestureDetector(
      onTap: () => _selectDate(context),
      child: Container(
        padding: EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.grey.shade200)
        ),
        child: Row(
          children: [
            Icon(Icons.calendar_today, color: Colors.indigo, size: 20),
            SizedBox(width: 15),
            Text('التاريخ: ${DateFormat('yyyy-MM-dd').format(selectedDate)}', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.indigo.shade900)),
            Spacer(),
            Icon(Icons.edit, color: Colors.grey, size: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountDropdown(String label, int? value, Function(int?) onChanged) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 20, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200)
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
          DropdownButtonHideUnderline(
            child: DropdownButton<int>(
              hint: Text('اختر الحساب'),
              value: value,
              isExpanded: true,
              items: widget.paymentMethods.map<DropdownMenuItem<int>>((pm) {
                return DropdownMenuItem<int>(
                  value: pm['id'],
                  child: Text('${pm['name']} (${pm['balance']} ${pm['currency']})'),
                );
              }).toList(),
              onChanged: onChanged,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField(TextEditingController ctrl, String label, IconData icon, TextInputType ktype) {
    return TextField(
      controller: ctrl,
      keyboardType: ktype,
      decoration: InputDecoration(
        prefixIcon: Icon(icon, color: Colors.indigo),
        labelText: label,
        filled: true,
        fillColor: Colors.white,
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide(color: Colors.grey.shade200)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide(color: Colors.indigo)),
      ),
    );
  }
}
