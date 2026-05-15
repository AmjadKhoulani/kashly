import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';
import 'fund_detail_screen.dart';

class FundsScreen extends StatefulWidget {
  @override
  _FundsScreenState createState() => _FundsScreenState();
}

class _FundsScreenState extends State<FundsScreen> {
  final apiService = ApiService();
  List? funds;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadFunds();
  }

  void loadFunds() async {
    final result = await apiService.getFunds();
    setState(() {
      funds = result;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('صناديق الاستثمار', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator()) 
        : RefreshIndicator(
            onRefresh: () async => loadFunds(),
            child: ListView.builder(
              padding: EdgeInsets.all(20),
              itemCount: funds?.length ?? 0,
              itemBuilder: (context, i) {
                final f = funds![i];
                return GestureDetector(
                  onTap: () => Get.to(() => FundDetailScreen(fundId: f['id'])),
                  child: Container(
                    margin: EdgeInsets.only(bottom: 20),
                    padding: EdgeInsets.all(25),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(30),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: Offset(0, 5))],
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Container(
                              width: 60, height: 60,
                              decoration: BoxDecoration(color: Colors.indigo.withOpacity(0.05), borderRadius: BorderRadius.circular(20)),
                              child: Center(child: Text(f['icon'] ?? '🏢', style: TextStyle(fontSize: 24))),
                            ),
                            SizedBox(width: 20),
                            Expanded(child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(f['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: Colors.indigo.shade900)),
                                Text('صندوق استثماري', style: TextStyle(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.bold)),
                              ],
                            )),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Text('${f['current_value']} ${f['currency']}', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Colors.indigo)),
                                Text('الرصيد الحالي', style: TextStyle(color: Colors.grey, fontSize: 10)),
                              ],
                            )
                          ],
                        ),
                        SizedBox(height: 20),
                        Row(
                          children: [
                            Expanded(child: Container(
                              height: 8,
                              decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(10)),
                              child: FractionallySizedBox(
                                widthFactor: 0.7, // Example growth
                                alignment: Alignment.centerRight,
                                child: Container(decoration: BoxDecoration(color: Colors.greenAccent.shade700, borderRadius: BorderRadius.circular(10))),
                              ),
                            )),
                            SizedBox(width: 15),
                            Text('+12%', style: TextStyle(color: Colors.greenAccent.shade700, fontWeight: FontWeight.w900, fontSize: 12)),
                          ],
                        )
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
    );
  }
}
