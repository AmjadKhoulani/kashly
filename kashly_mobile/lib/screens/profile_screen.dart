import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../api/api_service.dart';
import 'categories_screen.dart';
import 'wallets_screen.dart';
import 'businesses_screen.dart';

class ProfileScreen extends StatelessWidget {
  final apiService = ApiService();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'الملف الشخصي',
          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFF0F172A), fontSize: 18),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        physics: BouncingScrollPhysics(),
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 10),
        child: Column(
          children: [
            _buildProfileCard(),
            SizedBox(height: 25),
            _buildMenuSection(context),
            SizedBox(height: 35),
            _buildLogoutButton(),
            SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileCard() {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E1B4B)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: Color(0xFF1E1B4B).withOpacity(0.15),
            blurRadius: 20,
            offset: Offset(0, 10),
          )
        ],
      ),
      child: Column(
        children: [
          // User Avatar with circular premium border
          Container(
            padding: EdgeInsets.all(4),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: LinearGradient(
                colors: [Color(0xFF818CF8), Color(0xFF4F46E5)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: CircleAvatar(
              radius: 46,
              backgroundColor: Color(0xFF1E293B),
              child: Icon(Icons.person_rounded, size: 50, color: Color(0xFFC7D2FE)),
            ),
          ),
          SizedBox(height: 18),
          Text(
            'أهلاً بك مجدداً',
            style: GoogleFonts.almarai(
              color: Colors.white.withOpacity(0.55),
              fontSize: 11,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: 4),
          Text(
            'المستثمر الذكي',
            style: GoogleFonts.almarai(
              fontSize: 20,
              fontWeight: FontWeight.w900,
              color: Colors.white,
            ),
          ),
          SizedBox(height: 6),
          Container(
            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.06),
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: Colors.white.withOpacity(0.1), width: 1),
            ),
            child: Text(
              'حساب مستثمر نشط',
              style: GoogleFonts.almarai(
                color: Color(0xFF818CF8),
                fontSize: 9,
                fontWeight: FontWeight.w800,
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildMenuSection(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
          child: Text(
            'إدارة المنصة المالية',
            style: GoogleFonts.almarai(
              fontWeight: FontWeight.w900,
              fontSize: 13,
              color: Color(0xFF64748B),
            ),
          ),
        ),
        _menuItem(
          Icons.category_outlined,
          'إدارة التصنيفات المالية',
          'التحكم ببنود الصرف والدخل',
          Color(0xFF4F46E5),
          () => Get.to(() => CategoriesScreen()),
        ),
        _menuItem(
          Icons.account_balance_wallet_outlined,
          'إدارة المحافظ والحسابات',
          'إضافة وتعديل المحافظ المتاحة',
          Color(0xFF0284C7),
          () => Get.to(() => WalletsScreen()),
        ),
        _menuItem(
          Icons.business_center_outlined,
          'إدارة قطاع الأعمال والشركات',
          'استعراض الاستثمارات وحصص الشركات',
          Color(0xFFD97706),
          () => Get.to(() => BusinessesScreen()),
        ),
        Divider(color: Color(0xFFE2E8F0), height: 30, thickness: 1),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
          child: Text(
            'المزيد من الخصائص',
            style: GoogleFonts.almarai(
              fontWeight: FontWeight.w900,
              fontSize: 13,
              color: Color(0xFF64748B),
            ),
          ),
        ),
        _menuItem(
          Icons.picture_as_pdf_outlined,
          'تحميل التقرير الشهري',
          'تصدير كشف حساب مفصل PDF',
          Color(0xFFE11D48),
          () {
            Get.snackbar(
              'قريباً جداً',
              'ميزة إصدار وتصدير التقارير الذكية ستتوفر في التحديث القادم تلقائياً',
              backgroundColor: Color(0xFF0F172A).withOpacity(0.9),
              colorText: Colors.white,
              borderRadius: 15,
              margin: EdgeInsets.all(15),
            );
          },
        ),
        _menuItem(
          Icons.security_rounded,
          'إعدادات الحماية والأمان',
          'تغيير كلمة المرور وتوثيق البصمة',
          Color(0xFF059669),
          () {},
        ),
        _menuItem(
          Icons.notifications_active_outlined,
          'مركز التنبيهات المخصصة',
          'ضبط التنبيهات وتفاصيل المعاملات',
          Color(0xFF7C3AED),
          () {},
        ),
      ],
    );
  }

  Widget _menuItem(
    IconData icon,
    String label,
    String subtitle,
    Color accentColor,
    VoidCallback onTap,
  ) {
    return Container(
      margin: EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.01),
            blurRadius: 10,
            offset: Offset(0, 4),
          )
        ],
      ),
      child: ListTile(
        onTap: onTap,
        contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        leading: Container(
          padding: EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: accentColor.withOpacity(0.08),
            borderRadius: BorderRadius.circular(15),
          ),
          child: Icon(icon, color: accentColor, size: 20),
        ),
        title: Text(
          label,
          style: GoogleFonts.almarai(fontWeight: FontWeight.w800, fontSize: 13, color: Color(0xFF0F172A)),
        ),
        subtitle: Text(
          subtitle,
          style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF64748B), fontWeight: FontWeight.bold),
        ),
        trailing: Icon(Icons.arrow_forward_ios_rounded, size: 12, color: Color(0xFF94A3B8)),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
      ),
    );
  }

  Widget _buildLogoutButton() {
    return Container(
      width: double.infinity,
      child: TextButton(
        onPressed: () async {
          await apiService.logout();
          Get.offAllNamed('/login');
        },
        style: TextButton.styleFrom(
          backgroundColor: Color(0xFFFEE2E2),
          padding: EdgeInsets.symmetric(vertical: 18),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.logout_rounded, color: Color(0xFFDC2626), size: 20),
            SizedBox(width: 10),
            Text(
              'تسجيل خروج آمن من الحساب',
              style: GoogleFonts.almarai(fontWeight: FontWeight.w900, color: Color(0xFFDC2626), fontSize: 14),
            ),
          ],
        ),
      ),
    );
  }
}
