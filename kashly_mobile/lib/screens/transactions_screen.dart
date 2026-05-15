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
                          margin: EdgeInsets.only(bottom: 15),
                          padding: EdgeInsets.all(15),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
                          child: Row(
                            children: [
                              Container(
                                width: 50, height: 50,
                                decoration: BoxDecoration(
                                  color: t['type'] == 'income' ? Colors.green.withOpacity(0.1) : Colors.red.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(15)
                                ),
                                child: Icon(t['type'] == 'income' ? Icons.trending_up : Icons.trending_down, color: t['type'] == 'income' ? Colors.green : Colors.red),
                              ),
                              SizedBox(width: 15),
                              Expanded(child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(t['description'] ?? t['category'], style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                  Text('${t['category']} • ${t['transaction_date']}', style: TextStyle(color: Colors.grey, fontSize: 10)),
                                ],
                              )),
                              Text('${t['type'] == 'income' ? '+' : '-'}${t['amount']} ${t['payment_method']['currency']}', 
                                style: TextStyle(fontWeight: FontWeight.w900, color: t['type'] == 'income' ? Colors.green : Colors.red)),
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
      height: 60,
      padding: EdgeInsets.symmetric(horizontal: 20),
      child: ListView(
        scrollDirection: Axis.horizontal,
        children: [
          _filterChip('الكل', null, 'type'),
          _filterChip('دخل', 'income', 'type'),
          _filterChip('مصاريف', 'expense', 'type'),
          VerticalDivider(width: 30, indent: 15, endIndent: 15),
          ...categories.map((c) => _filterChip(c, c, 'category')).toList(),
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
        margin: EdgeInsets.only(right: 10, top: 10, bottom: 10),
        padding: EdgeInsets.symmetric(horizontal: 20),
        decoration: BoxDecoration(
          color: isSelected ? Colors.indigo : Colors.white,
          borderRadius: BorderRadius.circular(15),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
        ),
        child: Center(child: Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.indigo, fontWeight: FontWeight.bold, fontSize: 12))),
      ),
    );
  }
}
