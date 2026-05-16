import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'package:intl/intl.dart';

class AddTransactionScreen extends StatefulWidget {
  @override
  _AddTransactionScreenState createState() => _AddTransactionScreenState();
}

class _AddTransactionScreenState extends State<AddTransactionScreen> {
  final apiService = ApiService();
  final amountController = TextEditingController();
  final descController = TextEditingController();
  
  String type = 'expense';
  String? selectedAccountType;
  int? selectedAccountId;
  dynamic selectedCategory; // Now stores the whole category object
  DateTime selectedDate = DateTime.now();
  
  List funds = [];
  List wallets = [];
  List businesses = [];
  List allCategories = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadData();
  }

  void loadData() async {
    final dashboard = await apiService.getDashboard();
    final fundsList = await apiService.getFunds();
    final categoriesList = await apiService.getTransactionCategories();
    setState(() {
      wallets = dashboard?['wallets'] ?? [];
      businesses = dashboard?['businesses'] ?? [];
      funds = fundsList ?? [];
      allCategories = categoriesList ?? [];
      isLoading = false;
    });
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
    if (amountController.text.isEmpty || selectedAccountId == null || selectedCategory == null) {
      Get.snackbar('خطأ', 'يرجى إكمال كافة الحقول');
      return;
    }

    final data = {
      'amount': amountController.text,
      'type': type,
      'category_id': selectedCategory['id'],
      'category': selectedCategory['name'],
      'transaction_date': DateFormat('yyyy-MM-dd').format(selectedDate),
      'transactionable_id': selectedAccountId,
      'transactionable_type': selectedAccountType == 'fund' 
          ? 'App\\Models\\InvestmentFund' 
          : (selectedAccountType == 'business' ? 'App\\Models\\Business' : 'App\\Models\\Wallet'),
      'description': descController.text,
      'payment_method_id': selectedAccountType == 'wallet' ? selectedAccountId : null,
    };

    final success = await apiService.addTransaction(data);
    if (success) {
      Get.back(result: true);
      Get.snackbar('تم', 'تم تسجيل العملية بنجاح');
    } else {
      Get.snackbar('خطأ', 'فشل في حفظ العملية');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('إضافة عملية جديدة', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : SingleChildScrollView(
            padding: EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                _buildTypeSelector(),
                SizedBox(height: 20),
                _buildTextField(amountController, 'المبلغ', Icons.attach_money, TextInputType.number),
                SizedBox(height: 15),
                _buildCategorySelector(),
                SizedBox(height: 15),
                _buildDatePicker(),
                SizedBox(height: 15),
                _buildAccountSelector(),
                SizedBox(height: 15),
                _buildTextField(descController, 'الوصف (اختياري)', Icons.description, TextInputType.text),
                SizedBox(height: 30),
                ElevatedButton(
                  onPressed: save,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.indigo,
                    padding: EdgeInsets.symmetric(vertical: 20),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                  ),
                  child: Text('حفظ العملية', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
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
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
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

  Widget _buildCategorySelector() {
    final list = allCategories.where((c) {
      if (type == 'capital') return c['type'] == 'capital';
      return c['type'] == type;
    }).toList();
    
    // Reset selected if type changed and current selection is no longer valid
    if (selectedCategory != null && selectedCategory['type'] != type) {
      selectedCategory = null;
    }
    
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 20, vertical: 5),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<dynamic>(
          hint: Text('اختر التصنيف'),
          value: selectedCategory,
          isExpanded: true,
          items: list.map((c) => DropdownMenuItem(
            value: c, 
            child: Row(
              children: [
                Text(c['icon'] ?? '📁', style: TextStyle(fontSize: 18)),
                SizedBox(width: 15),
                Text(c['name'], style: TextStyle(fontWeight: FontWeight.bold, color: Colors.indigo.shade900)),
              ],
            )
          )).toList(),
          onChanged: (val) => setState(() => selectedCategory = val),
        ),
      ),
    );
  }

  Widget _buildTypeSelector() {
    return Row(
      children: [
        _typeButton('مصاريف', 'expense', Colors.red),
        SizedBox(width: 10),
        _typeButton('دخل', 'income', Colors.green),
        if (selectedAccountType == 'fund') ...[
          SizedBox(width: 10),
          _typeButton('رأس مال', 'capital', Colors.amber.shade700),
        ],
      ],
    );
  }

  Widget _typeButton(String label, String val, Color color) {
    bool isSelected = type == val;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() {
          type = val;
          selectedCategory = null;
        }),
        child: Container(
          padding: EdgeInsets.symmetric(vertical: 15),
          decoration: BoxDecoration(
            color: isSelected ? color : Colors.white,
            borderRadius: BorderRadius.circular(15),
            border: Border.all(color: color.withOpacity(0.3)),
          ),
          child: Center(child: Text(label, style: TextStyle(color: isSelected ? Colors.white : color, fontWeight: FontWeight.bold, fontSize: 12))),
        ),
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
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide.none),
      ),
    );
  }

  Widget _buildAccountSelector() {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          hint: Text('اختر المحفظة أو الصندوق'),
          value: selectedAccountId == null ? null : '$selectedAccountType-$selectedAccountId',
          isExpanded: true,
          items: [
            ...wallets.map((w) => DropdownMenuItem(
              value: 'wallet-${w['id']}',
              child: Row(children: [Icon(Icons.account_balance_wallet, size: 16), SizedBox(width: 10), Text('${w['name']} (${w['currency']})')]),
            )),
            ...businesses.map((b) => DropdownMenuItem(
              value: 'business-${b['id']}',
              child: Row(children: [Icon(Icons.storefront, size: 16), SizedBox(width: 10), Text('${b['name']} (${b['currency'] ?? 'USD'})')]),
            )),
            ...funds.map((f) => DropdownMenuItem(
              value: 'fund-${f['id']}',
              child: Row(children: [Icon(Icons.business_center, size: 16), SizedBox(width: 10), Text('${f['name']} (${f['currency']})')]),
            )),
          ],
          onChanged: (val) {
            if (val != null) {
              final parts = val.split('-');
              setState(() {
                selectedAccountType = parts[0];
                selectedAccountId = int.parse(parts[1]);
                
                // Safety check: if type was capital but new account is not a fund, reset type
                if (selectedAccountType != 'fund' && type == 'capital') {
                  type = 'expense';
                  selectedCategory = null;
                }
              });
            }
          },
        ),
      ),
    );
  }
}
