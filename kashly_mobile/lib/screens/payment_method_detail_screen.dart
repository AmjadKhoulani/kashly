import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';
import 'add_transaction_screen.dart';

class PaymentMethodDetailScreen extends StatefulWidget {
  final int paymentMethodId;
  PaymentMethodDetailScreen({required this.paymentMethodId});

  @override
  _PaymentMethodDetailScreenState createState() => _PaymentMethodDetailScreenState();
}

class _PaymentMethodDetailScreenState extends State<PaymentMethodDetailScreen> {
  final apiService = ApiService();
  Map<String, dynamic>? data;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadDetail();
  }

  void loadDetail() async {
    final result = await apiService.getPaymentMethodDetail(widget.paymentMethodId);
    setState(() {
      data = result;
      isLoading = false;
    });
  }

  void _confirmDelete() {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('حذف الحساب الفرعي', style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Colors.red.shade900)),
        content: Text('هل أنت متأكد من حذف هذا الحساب/البطاقة الفرعية نهائياً؟ سيؤدي ذلك لحذف كافة الحركات المرتبطة بها ولا يمكن استعادتها.', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 13)),
        actions: [
          TextButton(
            child: Text('إلغاء', style: GoogleFonts.almarai(color: Colors.grey, fontWeight: FontWeight.bold)),
            onPressed: () => Get.back(),
          ),
          TextButton(
            child: Text('حذف الحساب', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.bold)),
            onPressed: () async {
              Get.back();
              final success = await apiService.deletePaymentMethod(widget.paymentMethodId);
              if (success) {
                Get.back(result: true);
                Get.snackbar('تم بنجاح', 'تم حذف الحساب الفرعي بنجاح', backgroundColor: Colors.green, colorText: Colors.white);
              } else {
                Get.snackbar('خطأ', 'حدث خطأ أثناء حذف الحساب الفرعي', backgroundColor: Colors.red, colorText: Colors.white);
              }
            },
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final format = NumberFormat.decimalPattern('en_US');

    if (isLoading) {
      return Scaffold(
        backgroundColor: Color(0xFFF8FAFC),
        body: Center(child: CircularProgressIndicator(color: Color(0xFF3B82F6))),
      );
    }

    if (data == null) {
      return Scaffold(
        backgroundColor: Color(0xFFF8FAFC),
        appBar: AppBar(elevation: 0, backgroundColor: Colors.transparent),
        body: Center(child: Text('خطأ في تحميل تفاصيل الحساب الفرعي', style: GoogleFonts.almarai(fontWeight: FontWeight.bold))),
      );
    }

    final String name = data?['name'] ?? 'حساب فرعي';
    final String type = data?['type'] ?? 'cash';
    final String currency = data?['currency'] ?? 'USD';
    final double balance = double.tryParse(data?['balance']?.toString() ?? '0') ?? 0.0;
    
    // Parent wallet/fund name
    String parentLabel = 'محفظة رئيسية';
    String parentName = '';
    if (data?['wallet'] != null) {
      parentLabel = 'تابع للمحفظة';
      parentName = data?['wallet']['name'] ?? '';
    } else if (data?['fund'] != null) {
      parentLabel = 'تابع للاستثمار';
      parentName = data?['fund']['name'] ?? '';
    }

    String typeLabel = 'حساب كاش فرعي';
    IconData typeIcon = Icons.money_rounded;
    Color typeColor = Color(0xFF475569);
    
    if (type == 'bank') {
      typeLabel = 'حساب بنكي فرعي';
      typeIcon = Icons.account_balance_rounded;
      typeColor = Color(0xFF3B82F6);
    } else if (type == 'credit_card' || type == 'debit_card') {
      typeLabel = type == 'credit_card' ? 'بطاقة ائتمانية' : 'بطاقة دفع';
      typeIcon = Icons.credit_card_rounded;
      typeColor = Color(0xFF10B981);
    }

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: true,
        title: Text(
          name,
          style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontWeight: FontWeight.w900, fontSize: 16),
        ),
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: Color(0xFF475569), size: 20),
          onPressed: () => Get.back(),
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.delete_outline_rounded, color: Colors.red.shade600),
            onPressed: _confirmDelete,
          )
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => loadDetail(),
        child: SingleChildScrollView(
          physics: AlwaysScrollableScrollPhysics(),
          child: Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // 1. Hero Balance Card
                Container(
                  padding: EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [Color(0xFF1E293B), Color(0xFF334155), Color(0xFF475569)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: [
                      BoxShadow(color: Color(0xFF1E293B).withOpacity(0.12), blurRadius: 15, offset: Offset(0, 8))
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Container(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.12),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Row(
                              children: [
                                Icon(typeIcon, color: Colors.white, size: 14),
                                SizedBox(width: 6),
                                Text(
                                  typeLabel,
                                  style: GoogleFonts.almarai(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ),
                          Text(
                            currency,
                            style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontSize: 12, fontWeight: FontWeight.w900),
                          )
                        ],
                      ),
                      SizedBox(height: 20),
                      Text(
                        'الرصيد المتاح للحساب',
                        style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 5),
                      Text(
                        '${format.format(balance)} $currency',
                        style: GoogleFonts.almarai(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900, letterSpacing: -0.5),
                      ),
                      if (parentName.isNotEmpty) ...[
                        SizedBox(height: 15),
                        Divider(color: Colors.white.withOpacity(0.15)),
                        SizedBox(height: 10),
                        Row(
                          children: [
                            Text(
                              '$parentLabel: ',
                              style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.4), fontSize: 10, fontWeight: FontWeight.bold),
                            ),
                            Text(
                              parentName,
                              style: GoogleFonts.almarai(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w800),
                            )
                          ],
                        )
                      ]
                    ],
                  ),
                ),
                SizedBox(height: 25),

                // 2. Action Button
                GestureDetector(
                  onTap: () async {
                    final res = await Get.to(() => AddTransactionScreen(), arguments: {
                      'accountType': 'payment_method',
                      'accountId': widget.paymentMethodId,
                    });
                    if (res == true) loadDetail();
                  },
                  child: Container(
                    padding: EdgeInsets.symmetric(vertical: 16),
                    decoration: BoxDecoration(
                      color: Color(0xFF3B82F6).withOpacity(0.08),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Color(0xFF3B82F6).withOpacity(0.2), width: 1.5),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.add_circle_outline_rounded, color: Color(0xFF3B82F6), size: 18),
                        SizedBox(width: 8),
                        Text(
                          'تسجيل حركة جديدة بهذا الحساب',
                          style: GoogleFonts.almarai(color: Color(0xFF3B82F6), fontWeight: FontWeight.w900, fontSize: 13),
                        ),
                      ],
                    ),
                  ),
                ),
                SizedBox(height: 35),

                // 3. Transactions List Section
                Text(
                  '📊 الحركات والعمليات الداخلية',
                  style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF1E293B)),
                ),
                SizedBox(height: 15),
                _buildTransactionsSection(format),
                SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildTransactionsSection(NumberFormat format) {
    final txs = data?['transactions'] as List? ?? [];
    if (txs.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 60),
          child: Column(
            children: [
              Text('🕳️', style: TextStyle(fontSize: 32)),
              SizedBox(height: 10),
              Text('لا توجد حركات مسجلة حالياً', style: GoogleFonts.almarai(fontWeight: FontWeight.bold, color: Color(0xFF94A3B8), fontSize: 12)),
            ],
          ),
        ),
      );
    }

    return Column(
      children: txs.map((t) {
        final categoryRelationRaw = t['category_relation'];
        final Map<String, dynamic>? category = categoryRelationRaw != null ? Map<String, dynamic>.from(categoryRelationRaw) : null;

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
        final double amount = double.tryParse(t['amount']?.toString() ?? '0') ?? 0.0;
        final String type = t['type'] ?? 'expense';
        final String currency = t['currency'] ?? 'USD';

        Color iconColor = Color(int.parse(categoryColor.replaceFirst('#', '0xFF')));
        Color typeColor = type == 'income'
            ? Colors.green.shade600
            : (type == 'capital' ? Colors.indigo.shade600 : Colors.red.shade600);

        return Container(
          margin: EdgeInsets.only(bottom: 12),
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: Color(0xFFE2E8F0), width: 1.5),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.01), blurRadius: 15, offset: Offset(0, 5))
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: iconColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(child: Text(categoryIcon, style: TextStyle(fontSize: 18))),
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
                      categoryName,
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
        );
      }).toList(),
    );
  }
}
