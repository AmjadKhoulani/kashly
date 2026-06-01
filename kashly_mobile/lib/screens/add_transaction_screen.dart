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

  String type = 'expense';
  String? selectedAccountType; // 'wallet', 'fund', 'business'
  int? selectedAccountId;
  int? selectedPaymentMethodId; // selected sub-account for wallets
  dynamic selectedCategory;
  DateTime selectedDate = DateTime.now();

  // Context-mode: when opened from a specific screen
  bool _isContextMode = false; // true = opened from a detail screen
  String? _contextAccountName;

  List wallets = [];
  List funds = [];
  List businesses = [];
  List allCategories = [];
  List currentPaymentMethods = []; // list of sub-accounts for the currently selected wallet
  bool isLoading = true;

  // Derived: which types to show
  bool get _showCapital => selectedAccountType == 'fund';
  bool get _showAccountSelector => !_isContextMode;

  @override
  void initState() {
    super.initState();
    _parseArguments();
    loadData();
  }

  void _parseArguments() {
    if (Get.arguments != null && Get.arguments is Map) {
      final args = Get.arguments as Map;
      if (args.containsKey('accountType') && args.containsKey('accountId')) {
        final argType = args['accountType'] as String?;
        final argId = args['accountId'] as int?;
        _contextAccountName = args['accountName'] as String?;
        _isContextMode = true;

        if (argType == 'payment_method') {
          selectedAccountType = 'wallet';
          selectedPaymentMethodId = argId;
        } else {
          selectedAccountType = argType;
          selectedAccountId = argId;
        }

        // Default type per account type
        if (selectedAccountType == 'fund') {
          type = 'income';
        } else {
          type = 'expense';
        }
      }
    }
  }

  void _updatePaymentMethods() {
    if (selectedAccountType == 'wallet' && selectedAccountId != null) {
      final wallet = wallets.firstWhere(
        (w) => w['id'] == selectedAccountId,
        orElse: () => null,
      );
      if (wallet != null) {
        currentPaymentMethods = wallet['payment_methods'] as List? ?? [];
        if (currentPaymentMethods.isNotEmpty) {
          if (selectedPaymentMethodId == null ||
              !currentPaymentMethods.any((pm) => pm['id'] == selectedPaymentMethodId)) {
            selectedPaymentMethodId = currentPaymentMethods.first['id'] as int?;
          }
        } else {
          selectedPaymentMethodId = null;
        }
      } else {
        currentPaymentMethods = [];
        selectedPaymentMethodId = null;
      }
    } else {
      currentPaymentMethods = [];
      selectedPaymentMethodId = null;
    }
  }

  void loadData() async {
    try {
      final dashboard = await apiService.getDashboard();
      final fundsList = await apiService.getFunds();
      final categoriesList = await apiService.getTransactionCategories();
      setState(() {
        wallets = dashboard?['wallets'] ?? [];
        businesses = dashboard?['businesses'] ?? [];
        funds = fundsList ?? [];
        allCategories = categoriesList ?? [];

        // If we entered via payment_method, resolve the parent wallet
        if (_isContextMode && selectedPaymentMethodId != null && selectedAccountId == null) {
          for (var w in wallets) {
            final pms = w['payment_methods'] as List? ?? [];
            if (pms.any((pm) => pm['id'] == selectedPaymentMethodId)) {
              selectedAccountId = w['id'] as int?;
              _contextAccountName = w['name'] as String?;
              break;
            }
          }
        }

        _updatePaymentMethods();
        isLoading = false;
      });
    } catch (e) {
      print("Error loading AddTransactionScreen data: $e");
      setState(() {
        isLoading = false;
      });
      Get.snackbar('خطأ الاتصال', 'حدث خطأ أثناء تحميل البيانات من السيرفر',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
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
              primary: Colors.indigo,
              onPrimary: Colors.white,
              onSurface: Colors.indigo,
            ),
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
      Get.snackbar('حقول ناقصة', 'يرجى إدخال المبلغ وتحديد الحساب والتصنيف بالكامل',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
      return;
    }

    if (selectedAccountType == 'wallet' && selectedPaymentMethodId == null) {
      Get.snackbar('حساب فرعي مطلوب', 'يرجى تحديد الحساب/البطاقة الفرعية لإتمام العملية',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
      return;
    }

    String transactionableType;
    if (selectedAccountType == 'fund') {
      transactionableType = 'App\\Models\\InvestmentFund';
    } else if (selectedAccountType == 'business') {
      transactionableType = 'App\\Models\\Business';
    } else {
      transactionableType = 'App\\Models\\Wallet';
    }

    final data = {
      'amount': amountController.text,
      'type': type,
      'category_id': (selectedCategory['id'] == 0 || selectedCategory['id'] == null)
          ? null
          : selectedCategory['id'],
      'category': selectedCategory['name'],
      'transaction_date': DateFormat('yyyy-MM-dd').format(selectedDate),
      'transactionable_id': selectedAccountId,
      'transactionable_type': transactionableType,
      'description': descController.text,
      if (selectedPaymentMethodId != null) 'payment_method_id': selectedPaymentMethodId,
    };

    final success = await apiService.addTransaction(data);
    if (success) {
      Get.back(result: true);
      Get.snackbar('تم التسجيل ✅', 'تم تسجيل الحركة المالية وتحديث الأرصدة بنجاح',
          backgroundColor: Colors.green.shade50, colorText: Colors.green.shade800);
    } else {
      Get.snackbar('خطأ', 'فشل تسجيل الحركة المالية، يرجى التحقق',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
    }
  }

  // ─────────────────────────── UI ───────────────────────────

  @override
  Widget build(BuildContext context) {
    List currentAccountsList = [];
    if (selectedAccountType == 'wallet') currentAccountsList = wallets;
    if (selectedAccountType == 'fund') currentAccountsList = funds;
    if (selectedAccountType == 'business') currentAccountsList = businesses;

    // Screen title & subtitle based on context
    String screenTitle;
    String screenSubtitle;
    if (_isContextMode && selectedAccountType == 'wallet') {
      screenTitle = 'حركة مالية جديدة';
      screenSubtitle = _contextAccountName ?? 'المحفظة الشخصية';
    } else if (_isContextMode && selectedAccountType == 'fund') {
      screenTitle = 'حركة مالية على الكيان';
      screenSubtitle = _contextAccountName ?? 'صندوق الاستثمار';
    } else if (_isContextMode && selectedAccountType == 'business') {
      screenTitle = 'حركة مالية على المشروع';
      screenSubtitle = _contextAccountName ?? 'الأعمال والمشاريع';
    } else {
      screenTitle = 'تسجيل حركة مالية';
      screenSubtitle = 'اختر الحساب المستهدف';
    }

    return Scaffold(
      backgroundColor: Color(0xFFF4F6F9),
      appBar: AppBar(
        title: Column(
          children: [
            Text(
              screenTitle,
              style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 16),
            ),
            Text(
              screenSubtitle,
              style: GoogleFonts.almarai(color: Color(0xFF64748B), fontSize: 11, fontWeight: FontWeight.w700),
            ),
          ],
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
                  // 1. Type Selector (context-aware)
                  _buildTypeSelector(),
                  SizedBox(height: 20),

                  // 2. Amount & description
                  _buildAmountCard(),
                  SizedBox(height: 18),

                  // 3. Account selector (only if NOT in context mode)
                  if (_showAccountSelector) ...[
                    _buildAccountSelectorCard(currentAccountsList),
                    SizedBox(height: 18),
                  ],

                  // Sub-account Selector (always show if wallet is selected, regardless of context mode)
                  if (selectedAccountType == 'wallet') ...[
                    _buildSubAccountSelectorCard(),
                    SizedBox(height: 18),
                  ],

                  // 4. Category & Date
                  _buildCategoryDateCard(),
                  SizedBox(height: 30),

                  // 5. Save Button
                  _buildSaveButton(),
                  SizedBox(height: 50),
                ],
              ),
            ),
    );
  }

  // ─── Type Selector (adapts based on account type) ───
  Widget _buildTypeSelector() {
    final items = <Map<String, dynamic>>[
      {
        'label': 'مصروف',
        'sublabel': 'صادر',
        'value': 'expense',
        'icon': Icons.arrow_upward_rounded,
        'color': Colors.red.shade600,
        'bg': Color(0xFFFFF1F2),
        'border': Colors.red.shade100,
      },
      {
        'label': 'إيداع',
        'sublabel': 'وارد',
        'value': 'income',
        'icon': Icons.arrow_downward_rounded,
        'color': Colors.green.shade600,
        'bg': Color(0xFFECFDF5),
        'border': Colors.green.shade100,
      },
      if (_showCapital)
        {
          'label': 'رأس مال',
          'sublabel': 'استثماري',
          'value': 'capital',
          'icon': Icons.account_balance_outlined,
          'color': Colors.indigo.shade700,
          'bg': Color(0xFFEEF2FF),
          'border': Colors.indigo.shade100,
        },
    ];

    return Container(
      padding: EdgeInsets.all(6),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 12, offset: Offset(0, 4))],
      ),
      child: Row(
        children: items.map((item) {
          final bool selected = type == item['value'];
          return Expanded(
            child: GestureDetector(
              onTap: () => setState(() => type = item['value']),
              child: AnimatedContainer(
                duration: Duration(milliseconds: 200),
                padding: EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  color: selected ? item['bg'] : Colors.transparent,
                  borderRadius: BorderRadius.circular(16),
                  border: selected
                      ? Border.all(color: item['border'], width: 1.5)
                      : Border.all(color: Colors.transparent),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      item['icon'],
                      color: selected ? item['color'] : Color(0xFFCBD5E1),
                      size: 20,
                    ),
                    SizedBox(height: 5),
                    Text(
                      item['label'],
                      style: GoogleFonts.almarai(
                        color: selected ? item['color'] : Color(0xFF94A3B8),
                        fontWeight: FontWeight.w900,
                        fontSize: 12,
                      ),
                    ),
                    Text(
                      item['sublabel'],
                      style: GoogleFonts.almarai(
                        color: selected ? (item['color'] as Color).withOpacity(0.6) : Color(0xFFCBD5E1),
                        fontWeight: FontWeight.w700,
                        fontSize: 9,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }

  // ─── Amount & Description Card ───
  Widget _buildAmountCard() {
    Color typeColor = type == 'income'
        ? Colors.green.shade600
        : type == 'capital'
            ? Colors.indigo.shade600
            : Colors.red.shade600;

    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            'المبلغ',
            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 8),
          TextField(
            controller: amountController,
            keyboardType: TextInputType.numberWithOptions(decimal: true),
            style: GoogleFonts.almarai(fontSize: 36, fontWeight: FontWeight.w900, color: typeColor),
            decoration: InputDecoration(
              hintText: '0.00',
              hintStyle: GoogleFonts.almarai(fontSize: 36, color: Color(0xFFCBD5E1), fontWeight: FontWeight.w900),
              border: InputBorder.none,
              prefixIcon: Padding(
                padding: EdgeInsets.only(left: 8, right: 12),
                child: Icon(Icons.payments_outlined, color: typeColor, size: 26),
              ),
            ),
          ),
          Divider(color: Color(0xFFF1F5F9), height: 24, thickness: 1.5),
          Text(
            'الوصف (اختياري)',
            style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.bold, fontSize: 11),
          ),
          SizedBox(height: 8),
          TextField(
            controller: descController,
            style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
            decoration: InputDecoration(
              hintText: 'أضف تفاصيل أو ملاحظة...',
              hintStyle: GoogleFonts.almarai(fontSize: 12, color: Color(0xFF94A3B8)),
              border: InputBorder.none,
              prefixIcon: Icon(Icons.notes_rounded, color: Color(0xFF94A3B8), size: 20),
            ),
          ),
        ],
      ),
    );
  }

  // ─── Account Selector Card (only in general mode) ───
  Widget _buildAccountSelectorCard(List currentAccountsList) {
    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _sectionLabel('نوع الحساب المستهدف'),
          SizedBox(height: 10),
          DropdownButtonFormField<String>(
            value: selectedAccountType,
            decoration: InputDecoration(
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
            ),
            hint: Text('اختر نوع الحساب...', style: GoogleFonts.almarai(fontSize: 13, color: Color(0xFF94A3B8))),
            items: [
              DropdownMenuItem(value: 'wallet', child: Row(children: [Text('👛 '), Text('المحافظ الشخصية', style: GoogleFonts.almarai(fontSize: 13))])),
              DropdownMenuItem(value: 'business', child: Row(children: [Text('🏢 '), Text('الأعمال والمشاريع', style: GoogleFonts.almarai(fontSize: 13))])),
              DropdownMenuItem(value: 'fund', child: Row(children: [Text('📈 '), Text('الكيانات الاستثمارية', style: GoogleFonts.almarai(fontSize: 13))])),
            ],
            onChanged: (val) {
              setState(() {
                selectedAccountType = val;
                selectedAccountId = null;
                // Auto-set default type for funds
                if (val == 'fund') type = 'income';
                else type = 'expense';
                _updatePaymentMethods();
              });
            },
          ),

          if (selectedAccountType != null && currentAccountsList.isNotEmpty) ...[
            SizedBox(height: 18),
            _sectionLabel('الحساب المحدد'),
            SizedBox(height: 10),
            DropdownButtonFormField<int>(
              value: selectedAccountId,
              decoration: InputDecoration(
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
              ),
              hint: Text('اختر الحساب...', style: GoogleFonts.almarai(fontSize: 13, color: Color(0xFF94A3B8))),
              items: currentAccountsList.map((acc) {
                return DropdownMenuItem<int>(
                  value: acc['id'] as int,
                  child: Text(acc['name'].toString(), style: GoogleFonts.almarai(fontSize: 13)),
                );
              }).toList(),
              onChanged: (val) {
                setState(() {
                  selectedAccountId = val;
                  _updatePaymentMethods();
                });
              },
            ),
          ],
        ],
      ),
    );
  }

  // ─── Sub-account Selector Card ───
  Widget _buildSubAccountSelectorCard() {
    if (selectedAccountType != 'wallet' || currentPaymentMethods.isEmpty) return SizedBox.shrink();

    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _sectionLabel('الحساب الفرعي / طريقة الدفع'),
          SizedBox(height: 10),
          DropdownButtonFormField<int>(
            value: selectedPaymentMethodId,
            decoration: InputDecoration(
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
            ),
            hint: Text('اختر الحساب الفرعي...', style: GoogleFonts.almarai(fontSize: 13, color: Color(0xFF94A3B8))),
            items: currentPaymentMethods.map((pm) {
              final String pmType = pm['type'] ?? 'cash';
              final String pmIcon = pmType == 'bank' ? '🏛️' : (pmType == 'cash' ? '💵' : '💳');
              return DropdownMenuItem<int>(
                value: pm['id'] as int,
                child: Row(
                  children: [
                    Text('$pmIcon '),
                    Text('${pm['name']} (${pm['currency']})', style: GoogleFonts.almarai(fontSize: 13)),
                  ],
                ),
              );
            }).toList(),
            onChanged: (val) => setState(() => selectedPaymentMethodId = val),
          ),
        ],
      ),
    );
  }

  // ─── Category & Date Card ───
  Widget _buildCategoryDateCard() {
    // Filter categories by type
    final filtered = allCategories.where((c) {
      if (c == null || c['name'] == null) return false;
      if (c['type'] != null && c['type'] != type) return false;
      return true;
    }).toList();

    return Container(
      padding: EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _sectionLabel('التصنيف'),
          SizedBox(height: 10),
          DropdownButtonFormField<dynamic>(
            value: selectedCategory,
            decoration: InputDecoration(
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
              contentPadding: EdgeInsets.symmetric(horizontal: 15, vertical: 14),
            ),
            hint: Text('اختر التصنيف...', style: GoogleFonts.almarai(fontSize: 13, color: Color(0xFF94A3B8))),
            items: (filtered.isNotEmpty ? filtered : allCategories.where((c) => c != null && c['name'] != null).toList())
                .map((c) {
              return DropdownMenuItem<dynamic>(
                value: c,
                child: Row(
                  children: [
                    Text(c['icon'] ?? '📁', style: TextStyle(fontSize: 16)),
                    SizedBox(width: 8),
                    Flexible(child: Text(c['name'].toString(), style: GoogleFonts.almarai(fontSize: 13), overflow: TextOverflow.ellipsis)),
                  ],
                ),
              );
            }).toList(),
            onChanged: (val) => setState(() => selectedCategory = val),
          ),
          SizedBox(height: 18),

          _sectionLabel('التاريخ'),
          SizedBox(height: 10),
          InkWell(
            onTap: () => _selectDate(context),
            borderRadius: BorderRadius.circular(15),
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 15, vertical: 16),
              decoration: BoxDecoration(
                border: Border.all(color: Color(0xFFE2E8F0)),
                borderRadius: BorderRadius.circular(15),
              ),
              child: Row(
                children: [
                  Icon(Icons.calendar_month_outlined, color: Colors.indigo, size: 20),
                  SizedBox(width: 10),
                  Text(
                    DateFormat('yyyy-MM-dd').format(selectedDate),
                    style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                  ),
                  Spacer(),
                  Text('تغيير', style: GoogleFonts.almarai(fontSize: 11, color: Colors.indigo, fontWeight: FontWeight.w800)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─── Save Button ───
  Widget _buildSaveButton() {
    Color btnColor = type == 'income'
        ? Colors.green.shade600
        : type == 'capital'
            ? Colors.indigo.shade700
            : Colors.red.shade600;

    String btnLabel = type == 'income'
        ? 'تسجيل إيداع ✓'
        : type == 'capital'
            ? 'تسجيل رأس مال ✓'
            : 'تسجيل مصروف ✓';

    return ElevatedButton(
      onPressed: save,
      style: ElevatedButton.styleFrom(
        backgroundColor: btnColor,
        padding: EdgeInsets.symmetric(vertical: 18),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
        elevation: 4,
        shadowColor: btnColor.withOpacity(0.4),
      ),
      child: Text(
        btnLabel,
        style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15),
      ),
    );
  }

  Widget _sectionLabel(String text) {
    return Text(
      text,
      style: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontWeight: FontWeight.w800, fontSize: 11),
    );
  }
}
