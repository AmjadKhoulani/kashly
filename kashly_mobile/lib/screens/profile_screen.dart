import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../api/api_service.dart';

class ProfileScreen extends StatelessWidget {
  final apiService = ApiService();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('الحساب الشخصي', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(20),
        child: Column(
          children: [
            _buildProfileCard(),
            SizedBox(height: 30),
            _buildMenuSection(context),
            SizedBox(height: 40),
            _buildLogoutButton(),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileCard() {
    return Container(
      padding: EdgeInsets.all(30),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(40),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15)],
      ),
      child: Column(
        children: [
          CircleAvatar(
            radius: 50,
            backgroundColor: Colors.indigo.shade50,
            child: Icon(Icons.person, size: 50, color: Colors.indigo),
          ),
          SizedBox(height: 20),
          Text('أهلاً بك مجدداً', style: TextStyle(color: Colors.grey, fontSize: 14)),
          Text('المستثمر الذكي', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Colors.indigo.shade900)),
        ],
      ),
    );
  }

  Widget _buildMenuSection(BuildContext context) {
    return Column(
      children: [
        _menuItem(Icons.picture_as_pdf, 'تحميل التقرير الشهري', () {
          Get.snackbar('قريباً', 'ميزة التقارير سيتم تفعيلها في التحديث القادم');
        }),
        _menuItem(Icons.security, 'إعدادات الأمان', () {}),
        _menuItem(Icons.notifications, 'التنبيهات', () {}),
        _menuItem(Icons.help_outline, 'مركز المساعدة', () {}),
      ],
    );
  }

  Widget _menuItem(IconData icon, String label, VoidCallback onTap) {
    return Container(
      margin: EdgeInsets.only(bottom: 15),
      child: ListTile(
        onTap: onTap,
        leading: Container(
          padding: EdgeInsets.all(10),
          decoration: BoxDecoration(color: Colors.indigo.withOpacity(0.05), borderRadius: BorderRadius.circular(12)),
          child: Icon(icon, color: Colors.indigo, size: 20),
        ),
        title: Text(label, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
        trailing: Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
        tileColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }

  Widget _buildLogoutButton() {
    return ElevatedButton(
      onPressed: () async {
        await apiService.logout();
        Get.offAllNamed('/login');
      },
      style: ElevatedButton.styleFrom(
        backgroundColor: Colors.red.shade50,
        foregroundColor: Colors.red,
        elevation: 0,
        padding: EdgeInsets.symmetric(horizontal: 40, vertical: 15),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.logout, size: 20),
          SizedBox(width: 10),
          Text('تسجيل الخروج', style: TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}
