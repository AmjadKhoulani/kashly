import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';

class AddTransactionScreen extends StatefulWidget {
  @override
  _AddTransactionScreenState createState() => _AddTransactionScreenState();
}

class _AddTransactionScreenState extends State<AddTransactionScreen> {
  final apiService = ApiService();
  final amountController = TextEditingController();
  final descController = TextEditingController();
  final exchangeRateController = TextEditingController(text: '1.0');

  String type = 'expense'; // 'income', 'expense', 'capital'
  String? selectedAccountType;
  int? selectedAccountId;
  bool payInAlternative = false;
  String selectedAltCurrency = 'USD';
  dynamic selectedCategory;
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
      Get.snackbar('حقول ناقصة', 'يرجى إدخال المبلغ وتحديد الحساب والتصنيف بالكامل');
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
    };

    final success = await apiService.addTransaction(data);
    if (success) {
      Get.back(result: true);
      Get.snackbar('تم التسجيل', 'تم تسجيل الحركة المالية وتحديث الأرصدة بنجاح');
    } else {
      Get.snackbar('خطأ', 'فشل تسجيل الحركة المالية، يرجى التحقق');
    }
  }

  @override
  Widget build(BuildContext context) {
    List currentAccountsList = [];
    if (selectedAccountType == 'wallet') currentAccountsList = wallets;
    if (selectedAccountType == 'fund') currentAccountsList = funds;
    if (selectedAccountType == 'business') currentAccountsList = businesses;

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Text(
          'تسجيل حركة مالية جديدة',
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
          ? Center(child: CircularProgressIndicator(color: Colors.indigo))
          : SingleChildScrollView(
              physics: BouncingScrollPhysics(),
              padding: EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // 1. Transaction Type Segmented buttons
                  _buildTypeSegmentedSelector(),
                  SizedBox(height: 25),

                  // 2. Main Input Card (Amount & Description)
                  _buildMainInputCard(),
                  SizedBox(height: 25),

                  // 3. Selection Parameters Card (Account type, Account, Category, Date)
                  _buildSelectionParametersCard(currentAccountsList),
                  SizedBox(height: 35),

                  // 4. Save Button
                  ElevatedButton(
                    onPressed: save,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.indigo,
                      padding: EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
                      elevation: 4,
                      shadowColor: Colors.indigo.withOpacity(0.35),
                    ),
                    child: Text(
                      'تسجيل الحركة وتحديث الأرصدة',
                      style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15),
                    ),
                  ),
                  SizedBox(height: 50),
                ],
              ),
            ),
    );
  }

  Widget _buildTypeSegmentedSelector() {
    return Container(
      padding: EdgeInsets.all(5),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
      ),
      child: Row(
        children: [
          _buildSegmentButton('مصروف (صادر)', 'expense', Colors.red.shade700, Color(0xFFFFF1F2)),
          _buildSegmentButton('إيداع (وارد)', 'income', Colors.green.shade700, Color(0xFFECFDF5)),
          _buildSegmentButton('رأس مال', 'capital', Colors.indigo.shade700, Color(0xFFEEF2FF)),
        ],
      ),
    );
  }

  Widget _buildSegmentButton(String label, String value, Color color, Color bg) {
    final bool isSelected = type == value;
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            type = value;
          });
        },
        child: AnimatedContainer(
          duration: Duration(milliseconds: 200),
          padding: EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: isSelected ? bg : Colors.transparent,
            borderRadius: BorderRadius.circular(15),
          ),
          child: Center(
            child: Text(
              label,
              style: GoogleFonts.almarai(
                color: isSelected ? color : Color(0xFF64748B),
                fontWeight: FontWeight.w900,
                fontSize: 11,
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildMainInputCard() {
    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            'المبلغ المالي للحركة',
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 10),
          TextField(
            controller: amountController,
            keyboardType: TextInputType.number,
            style: GoogleFonts.almarai(fontSize: 32, fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
            decoration: InputDecoration(
              hintText: '0.00',
              hintStyle: GoogleFonts.almarai(fontSize: 32, color: Color(0xFFCBD5E1)),
              border: InputBorder.none,
              prefixIcon: Icon(Icons.payments_outlined, color: Colors.indigo, size: 28),
            ),
          ),
          Divider(color: Color(0xFFF1F5F9), height: 30, thickness: 1.5),
          Text(
            'التفاصيل والوصف الإضافي',
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 10),
          TextField(
            controller: descController,
            style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
            decoration: InputDecoration(
              hintText: 'مثال: شراء مواد خام للمشروع، عشاء عمل...',
              hintStyle: GoogleFonts.almarai(fontSize: 12, color: Color(0xFF94A3B8)),
              border: InputBorder.none,
              prefixIcon: Icon(Icons.description_outlined, color: Color(0xFF64748B), size: 20),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSelectionParametersCard(List currentAccountsList) {
    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // A. Account Type Selection
          Text(
            'تصنيف الحساب المالي المستهدف',
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 10),
          DropdownButtonFormField<String>(
            value: selectedAccountType,
            decoration: InputDecoration(
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
            ),
            items: [
              DropdownMenuItem(value: 'wallet', child: Text('المحافظ الشخصية', style: GoogleFonts.almarai(fontSize: 13))),
              DropdownMenuItem(value: 'business', child: Text('قطاع الأعمال والمشاريع', style: GoogleFonts.almarai(fontSize: 13))),
              DropdownMenuItem(value: 'fund', child: Text('صناديق الاستثمار المشترك', style: GoogleFonts.almarai(fontSize: 13))),
            ],
            onChanged: (val) {
              setState(() {
                selectedAccountType = val;
                selectedAccountId = null;
              });
            },
          ),
          SizedBox(height: 20),

          // B. Target Account Selection
          if (selectedAccountType != null) ...[
            Text(
              'الحساب المالي التفصيلي',
              style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
            ),
            SizedBox(height: 10),
            DropdownButtonFormField<int>(
              value: selectedAccountId,
              decoration: InputDecoration(
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
              ),
              items: currentAccountsList.map((acc) {
                return DropdownMenuItem<int>(
                  value: acc['id'] as int,
                  child: Text(acc['name'].toString(), style: GoogleFonts.almarai(fontSize: 13)),
                );
              }).toList(),
              onChanged: (val) => setState(() => selectedAccountId = val),
            ),
            SizedBox(height: 20),
          ],

          // C. Category Selection
          Text(
            'التصنيف المالي للحركة',
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 10),
          DropdownButtonFormField<dynamic>(
            value: selectedCategory,
            decoration: InputDecoration(
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
            ),
            items: allCategories.where((c) => c != null && c['name'] != null).map((c) {
              return DropdownMenuItem<dynamic>(
                value: c,
                child: Row(
                  children: [
                    Text(c['icon'] ?? '📁', style: TextStyle(fontSize: 16)),
                    SizedBox(width: 8),
                    Text(c['name'].toString(), style: GoogleFonts.almarai(fontSize: 13)),
                  ],
                ),
              );
            }).toList(),
            onChanged: (val) => setState(() => selectedCategory = val),
          ),
          SizedBox(height: 20),

          // D. Date Selection
          Text(
            'تاريخ تسجيل الحركة',
            style: GoogleFonts.almarai(color: Color(0xFF64748B), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 10),
          InkWell(
            onTap: () => _selectDate(context),
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 15, vertical: 16),
              decoration: BoxDecoration(
                border: Border.all(color: Color(0xFFCBD5E1)),
                borderRadius: BorderRadius.circular(15),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    DateFormat('yyyy-MM-dd').format(selectedDate),
                    style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                  ),
                  Icon(Icons.calendar_month_outlined, color: Colors.indigo, size: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
