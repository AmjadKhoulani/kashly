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
                    margin: EdgeInsets.only(bottom: 25),
                    padding: EdgeInsets.all(28),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(35),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: Offset(0, 8))],
                      border: Border.all(color: Colors.indigo.withOpacity(0.05))
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Container(
                              width: 70, height: 70,
                              decoration: BoxDecoration(
                                color: Colors.indigo.withOpacity(0.1), 
                                borderRadius: BorderRadius.circular(22),
                                border: Border.all(color: Colors.indigo.withOpacity(0.1))
                              ),
                              child: Center(child: Text(f['icon'] ?? '🏢', style: TextStyle(fontSize: 32))),
                            ),
                            SizedBox(width: 22),
                            Expanded(child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(f['name'], style: TextStyle(fontWeight: FontWeight.w900, fontSize: 22, color: Colors.indigo.shade900)),
                                SizedBox(height: 4),
                                Text('صندوق استثماري نشط'.toUpperCase(), style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 14, fontWeight: FontWeight.w900)),
                              ],
                            )),
                          ],
                        ),
                        SizedBox(height: 25),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('الرصيد الحالي'.toUpperCase(), style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 12, fontWeight: FontWeight.w900)),
                                SizedBox(height: 6),
                                Text('${f['current_value']} ${f['currency']}', 
                                  style: TextStyle(fontWeight: FontWeight.w900, fontSize: 24, color: Colors.blueGrey.shade900, letterSpacing: -1.0)),
                              ],
                            ),
                            Container(
                              padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(color: Colors.green.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
                              child: Text('+12.5%', style: TextStyle(color: Colors.green.shade700, fontWeight: FontWeight.w900, fontSize: 14)),
                            ),
                          ],
                        ),
                        SizedBox(height: 20),
                        Container(
                          height: 10,
                          decoration: BoxDecoration(color: Colors.blueGrey.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.blueGrey.shade100)),
                          child: FractionallySizedBox(
                            widthFactor: 0.7,
                            alignment: Alignment.centerRight,
                            child: Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(colors: [Colors.green.shade600, Colors.green.shade400]),
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: [BoxShadow(color: Colors.green.withOpacity(0.2), blurRadius: 5)]
                              ),
                            ),
                          ),
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
