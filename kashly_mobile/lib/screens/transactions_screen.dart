import 'package:flutter/material.dart';
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('سجل العمليات', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: Column(
        children: [
          _buildFilters(),
          Expanded(
            child: isLoading 
              ? Center(child: CircularProgressIndicator()) 
              : transactions.isEmpty 
                ? Center(child: Text('لا توجد عمليات تطابق البحث'))
                : RefreshIndicator(
                    onRefresh: () async => loadTransactions(),
                    child: ListView.builder(
                      padding: EdgeInsets.all(20),
                      itemCount: transactions.length,
                      itemBuilder: (context, i) {
                        final t = transactions[i];
                        return Container(
                          margin: EdgeInsets.only(bottom: 18),
                          padding: EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            color: Colors.white, 
                            borderRadius: BorderRadius.circular(25),
                            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: Offset(0, 4))]
                          ),
                          child: Row(
                            children: [
                              Container(
                                width: 55, height: 55,
                                decoration: BoxDecoration(
                                  color: t['category_id'] != null && t['category'] != null
                                    ? Color(int.parse(t['category']['color'].replaceFirst('#', '0xFF'))).withOpacity(0.15) 
                                    : (t['type'] == 'income' ? Colors.green.withOpacity(0.15) : Colors.red.withOpacity(0.15)),
                                  borderRadius: BorderRadius.circular(18)
                                ),
                                child: Center(child: Text(t['category_id'] != null && t['category'] != null ? t['category']['icon'] : (t['type'] == 'income' ? '↓' : '↑'), style: TextStyle(fontSize: 24))),
                              ),
                              SizedBox(width: 18),
                              Expanded(child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(t['description'] ?? (t['category_id'] != null && t['category'] != null ? t['category']['name'] : t['category']), 
                                    style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.blueGrey.shade800)),
                                  SizedBox(height: 4),
                                  Text('${t['category_id'] != null && t['category'] != null ? t['category']['name'] : t['category']} • ${t['transaction_date']}', 
                                    style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 12, fontWeight: FontWeight.bold)),
                                ],
                              )),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Text('${t['type'] == 'income' ? '+' : '-'}${t['amount']}', 
                                    style: TextStyle(fontWeight: FontWeight.w900, fontSize: 19, color: t['type'] == 'income' ? Colors.green.shade600 : Colors.red.shade600)),
                                  Text('${t['payment_method'] != null ? t['payment_method']['currency'] : (t['currency'] ?? '')}', 
                                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey.shade300)),
                                ],
                              ),
                            ],
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
      height: 70,
      padding: EdgeInsets.symmetric(horizontal: 20),
      child: ListView(
        scrollDirection: Axis.horizontal,
        children: [
          _filterChip('الكل', null, 'type'),
          _filterChip('دخل', 'income', 'type'),
          _filterChip('مصاريف', 'expense', 'type'),
          VerticalDivider(width: 30, indent: 20, endIndent: 20, color: Colors.blueGrey.shade100),
          ...categories.map((c) => _filterChip(c['name'], c['name'], 'category')).toList(),
        ],
      ),
    );
  }

  Widget _filterChip(String label, String? value, String filterType) {
    bool isSelected = filterType == 'type' ? selectedType == value : selectedCategory == value;
    return GestureDetector(
      onTap: () {
        setState(() {
          if (filterType == 'type') selectedType = value;
          else selectedCategory = value;
        });
        loadTransactions();
      },
      child: Container(
        margin: EdgeInsets.only(right: 12, top: 12, bottom: 12),
        padding: EdgeInsets.symmetric(horizontal: 22),
        decoration: BoxDecoration(
          color: isSelected ? Colors.indigo : Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8, offset: Offset(0, 2))],
          border: Border.all(color: isSelected ? Colors.indigo : Colors.indigo.withOpacity(0.05))
        ),
        child: Center(child: Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.indigo, fontWeight: FontWeight.w900, fontSize: 14))),
      ),
    );
  }
}
