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
  final exchangeRateController = TextEditingController(text: '1.0');
  
  String type = 'expense';
  String? selectedAccountType;
  int? selectedAccountId;
  int? selectedSubAccountId;
  bool payInAlternative = false;
  String selectedAltCurrency = 'USD';
  dynamic selectedCategory; // Now stores the whole category object
  DateTime selectedDate = DateTime.now();
  
  List wallets = [];
  List funds = [];
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
      
      // Auto pre-select account from arguments
      if (Get.arguments != null && Get.arguments is Map) {
        final args = Get.arguments as Map;
        if (args.containsKey('accountType') && args.containsKey('accountId')) {
          selectedAccountType = args['accountType'];
          selectedAccountId = args['accountId'];
          if (selectedAccountType == 'fund') {
            type = 'capital'; // Default to capital for pooled investment funds
          }
        }
      }
      
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
      'category_id': (selectedCategory['id'] == 0 || selectedCategory['id'] == null) ? null : selectedCategory['id'],
      'category': selectedCategory['name'],
      'transaction_date': DateFormat('yyyy-MM-dd').format(selectedDate),
      'transactionable_id': selectedAccountId,
      'transactionable_type': selectedAccountType == 'fund' 
          ? 'App\\Models\\InvestmentFund' 
          : (selectedAccountType == 'business' ? 'App\\Models\\Business' : 'App\\Models\\Wallet'),
      'description': descController.text,
      'payment_method_id': selectedAccountType == 'wallet' ? selectedSubAccountId : null,
      if (payInAlternative) ...{
        'currency': selectedAltCurrency,
        'exchange_rate': exchangeRateController.text,
      }
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
                if (selectedAccountType == 'wallet') ...[
                  SizedBox(height: 15),
                  _buildSubAccountSelector(),
                ],
                SizedBox(height: 15),
                _buildMultiCurrencySection(),
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
    var list = allCategories.where((c) {
      if (type == 'capital') return c['type'] == 'capital';
      return c['type'] == type;
    }).toList();

    // Dynamic Fallback: If type is capital but there are no capital categories in the DB,
    // inject a default category so the user can select it and save successfully.
    if (type == 'capital' && list.isEmpty) {
      final fallbackCategory = {
        'id': 0, // id 0 will be translated to null category_id in save()
        'name': 'رأس مال مساهم',
        'type': 'capital',
        'icon': '🏢'
      };
      list = [fallbackCategory];
      
      // Auto-select the fallback category to make UX seamless
      if (selectedCategory == null) {
        selectedCategory = fallbackCategory;
      }
    }
    
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
                selectedSubAccountId = null; // Reset sub-account selection when wallet changes
                
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

  Widget _buildSubAccountSelector() {
    dynamic wallet;
    for (var w in wallets) {
      if (w['id'] == selectedAccountId) {
        wallet = w;
        break;
      }
    }
    final subAccounts = wallet?['payment_methods'] as List? ?? [];

    return Container(
      padding: EdgeInsets.symmetric(horizontal: 15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<int?>(
          hint: Text('اختر الحساب الفرعي / العهدة'),
          value: selectedSubAccountId,
          isExpanded: true,
          items: [
            DropdownMenuItem<int?>(
              value: null,
              child: Row(
                children: [
                  Icon(Icons.account_balance_wallet_outlined, size: 16, color: Colors.indigo),
                  SizedBox(width: 10),
                  Text('الرصيد الرئيسي للمحفظة (${wallet?['currency'] ?? ''})'),
                ],
              ),
            ),
            ...subAccounts.map<DropdownMenuItem<int?>>((sa) => DropdownMenuItem<int?>(
              value: sa['id'],
              child: Row(
                children: [
                  Icon(sa['type'] == 'bank' ? Icons.account_balance : Icons.person_outline, size: 16, color: Colors.indigo),
                  SizedBox(width: 10),
                  Text('${sa['name']} (${sa['currency']}) ${sa['custodian_name'] != null && sa['custodian_name'].toString().isNotEmpty ? "- عهدة: " + sa['custodian_name'] : ""}'),
                ],
              ),
            )),
          ],
          onChanged: (val) {
            setState(() {
              selectedSubAccountId = val;
            });
          },
        ),
      ),
    );
  }

  Widget _buildMultiCurrencySection() {
    return Container(
      padding: EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'الدفع / الاستلام بعملة بديلة',
                style: TextStyle(fontWeight: FontWeight.bold, color: Colors.indigo.shade900),
              ),
              Switch(
                value: payInAlternative,
                onChanged: (val) {
                  setState(() {
                    payInAlternative = val;
                  });
                },
                activeColor: Colors.indigo,
              ),
            ],
          ),
          if (payInAlternative) ...[
            SizedBox(height: 15),
            Row(
              children: [
                Expanded(
                  child: Container(
                    padding: EdgeInsets.symmetric(horizontal: 10),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade50,
                      borderRadius: BorderRadius.circular(15),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        value: selectedAltCurrency,
                        items: ['USD', 'TRY', 'SAR', 'EUR'].map((curr) => DropdownMenuItem(
                          value: curr,
                          child: Text(curr, style: TextStyle(fontWeight: FontWeight.bold)),
                        )).toList(),
                        onChanged: (val) {
                          if (val != null) {
                            setState(() {
                              selectedAltCurrency = val;
                            });
                          }
                        },
                      ),
                    ),
                  ),
                ),
                SizedBox(width: 15),
                Expanded(
                  flex: 2,
                  child: TextField(
                    controller: exchangeRateController,
                    keyboardType: TextInputType.numberWithOptions(decimal: true),
                    decoration: InputDecoration(
                      labelText: 'سعر الصرف',
                      filled: true,
                      fillColor: Colors.grey.shade50,
                      contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 10),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15),
                        borderSide: BorderSide(color: Colors.grey.shade200),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15),
                        borderSide: BorderSide(color: Colors.grey.shade200),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }
}

