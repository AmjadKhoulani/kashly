import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dashboard_screen.dart';
import 'wallets_screen.dart';
import 'funds_screen.dart';
import 'debts_screen.dart';
import 'transactions_screen.dart';
import 'profile_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  @override
  _MainNavigationScreenState createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;
  
  final List<Widget> _screens = [
    DashboardScreen(),
    WalletsScreen(),
    FundsScreen(),
    DebtsScreen(),
    TransactionsScreen(),
    ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 20,
              offset: Offset(0, -4),
            )
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (index) => setState(() => _currentIndex = index),
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          selectedItemColor: Color(0xFF4F46E5), // Premium Indigo
          unselectedItemColor: Color(0xFF94A3B8), // Slate 400
          showSelectedLabels: true,
          showUnselectedLabels: true,
          selectedLabelStyle: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 9, color: Color(0xFF4F46E5)),
          unselectedLabelStyle: GoogleFonts.almarai(fontWeight: FontWeight.bold, fontSize: 9, color: Color(0xFF94A3B8)),
          items: [
            BottomNavigationBarItem(icon: Icon(Icons.dashboard_rounded), label: 'الرئيسية'),
            BottomNavigationBarItem(icon: Icon(Icons.account_balance_wallet_rounded), label: 'المحافظ'),
            BottomNavigationBarItem(icon: Icon(Icons.business_center_rounded), label: 'الصناديق'),
            BottomNavigationBarItem(icon: Icon(Icons.balance_rounded), label: 'الديون'),
            BottomNavigationBarItem(icon: Icon(Icons.receipt_long_rounded), label: 'العمليات'),
            BottomNavigationBarItem(icon: Icon(Icons.person_rounded), label: 'حسابي'),
          ],
        ),
      ),
    );
  }
}
