import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../api/api_service.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final apiService = ApiService();
  bool isLoading = false;

  void handleLogin() async {
    setState(() => isLoading = true);
    final token = await apiService.login(emailController.text, passwordController.text);
    setState(() => isLoading = false);

    if (token != null) {
      Get.offAllNamed('/dashboard');
    } else {
      Get.snackbar(
        'فشل تسجيل الدخول',
        'يرجى التأكد من صحة البريد الإلكتروني وكلمة المرور',
        backgroundColor: Colors.redAccent.withOpacity(0.9),
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        margin: EdgeInsets.all(15),
        borderRadius: 15,
        duration: Duration(seconds: 4),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Color(0xFF0F172A), Color(0xFF1E1B4B), Color(0xFF312E81)],
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            physics: BouncingScrollPhysics(),
            padding: EdgeInsets.all(30),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Logo Icon
                Container(
                  padding: EdgeInsets.all(22),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.04),
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white.withOpacity(0.1), width: 1.5),
                  ),
                  child: Icon(Icons.account_balance_wallet_rounded, size: 70, color: Colors.indigo.shade200),
                ),
                SizedBox(height: 25),
                Text(
                  'كاشلي.',
                  textAlign: TextAlign.center,
                  style: GoogleFonts.almarai(
                    color: Colors.white,
                    fontSize: 34,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -1.0,
                  ),
                ),
                SizedBox(height: 8),
                Text(
                  'منصتك المالية الموحدة والذكية',
                  textAlign: TextAlign.center,
                  style: GoogleFonts.almarai(
                    color: Colors.white.withOpacity(0.5),
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 45),

                // Glassmorphic Input Fields Box
                Container(
                  padding: EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.03),
                    borderRadius: BorderRadius.circular(30),
                    border: Border.all(color: Colors.white.withOpacity(0.08), width: 1.5),
                  ),
                  child: Column(
                    children: [
                      // Email Field
                      TextField(
                        controller: emailController,
                        style: GoogleFonts.almarai(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold),
                        decoration: InputDecoration(
                          hintText: 'البريد الإلكتروني',
                          hintStyle: GoogleFonts.almarai(color: Colors.white.withOpacity(0.45), fontSize: 12),
                          filled: true,
                          fillColor: Colors.white.withOpacity(0.03),
                          contentPadding: EdgeInsets.symmetric(horizontal: 20, vertical: 18),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.white.withOpacity(0.05)),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.white.withOpacity(0.05)),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.indigo.shade300.withOpacity(0.5), width: 1.5),
                          ),
                          prefixIcon: Icon(Icons.email_outlined, color: Colors.indigo.shade200, size: 20),
                        ),
                      ),
                      SizedBox(height: 18),

                      // Password Field
                      TextField(
                        controller: passwordController,
                        obscureText: true,
                        style: GoogleFonts.almarai(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold),
                        decoration: InputDecoration(
                          hintText: 'كلمة المرور الآمنة',
                          hintStyle: GoogleFonts.almarai(color: Colors.white.withOpacity(0.45), fontSize: 12),
                          filled: true,
                          fillColor: Colors.white.withOpacity(0.03),
                          contentPadding: EdgeInsets.symmetric(horizontal: 20, vertical: 18),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.white.withOpacity(0.05)),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.white.withOpacity(0.05)),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(18),
                            borderSide: BorderSide(color: Colors.indigo.shade300.withOpacity(0.5), width: 1.5),
                          ),
                          prefixIcon: Icon(Icons.lock_outline_rounded, color: Colors.indigo.shade200, size: 20),
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 35),

                // Login Button
                ElevatedButton(
                  onPressed: isLoading ? null : handleLogin,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: Color(0xFF0F172A),
                    padding: EdgeInsets.symmetric(vertical: 18),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    elevation: 5,
                    shadowColor: Colors.indigo.shade900.withOpacity(0.3),
                  ),
                  child: isLoading
                      ? SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(color: Color(0xFF0F172A), strokeWidth: 2.5),
                        )
                      : Text(
                          'تسجيل دخول آمن',
                          style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 15),
                        ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
