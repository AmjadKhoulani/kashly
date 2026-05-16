import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'https://kashly.xyz/api';

  Future<String?> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'mobile_app',
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      final token = data['token'];
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', token);
      return token;
    }
    return null;
  }

  Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  Future<Map<String, dynamic>?> getDashboard() async {
    final response = await http.get(Uri.parse('$baseUrl/dashboard'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<List?> getFunds() async {
    final response = await http.get(Uri.parse('$baseUrl/funds'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<Map<String, dynamic>?> getFundDetail(int id) async {
    final response = await http.get(Uri.parse('$baseUrl/funds/$id'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<List?> getBusinesses() async {
    final response = await http.get(Uri.parse('$baseUrl/businesses'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<Map<String, dynamic>?> getBusinessDetail(int id) async {
    final response = await http.get(Uri.parse('$baseUrl/businesses/$id'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<List?> getWallets() async {
    final response = await http.get(Uri.parse('$baseUrl/wallets'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<Map<String, dynamic>?> getWalletDetail(int id) async {
    final response = await http.get(Uri.parse('$baseUrl/wallets/$id'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<Map<String, dynamic>?> getTransactions({String? type, String? category, int page = 1}) async {
    var url = '$baseUrl/transactions?page=$page';
    if (type != null) url += '&type=$type';
    if (category != null) url += '&category=$category';
    final response = await http.get(Uri.parse(url), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<List?> getTransactionCategories() async {
    final response = await http.get(Uri.parse('$baseUrl/categories'), headers: await _getHeaders());
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  Future<bool> addTransaction(Map<String, dynamic> data) async {
    final response = await http.post(Uri.parse('$baseUrl/transactions'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200 || response.statusCode == 201;
  }

  Future<bool> addWallet(Map<String, dynamic> data) async {
    final response = await http.post(Uri.parse('$baseUrl/wallets'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200 || response.statusCode == 201;
  }

  Future<bool> updateWallet(int id, Map<String, dynamic> data) async {
    final response = await http.put(Uri.parse('$baseUrl/wallets/$id'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200;
  }

  Future<bool> addBusiness(Map<String, dynamic> data) async {
    final response = await http.post(Uri.parse('$baseUrl/businesses'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200 || response.statusCode == 201;
  }

  Future<bool> updateBusiness(int id, Map<String, dynamic> data) async {
    final response = await http.put(Uri.parse('$baseUrl/businesses/$id'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200;
  }

  Future<bool> transfer(Map<String, dynamic> data) async {
    final response = await http.post(Uri.parse('$baseUrl/transactions/transfer'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200 || response.statusCode == 201;
  }

  Future<bool> addCategory(Map<String, dynamic> data) async {
    final response = await http.post(Uri.parse('$baseUrl/categories'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200 || response.statusCode == 201;
  }

  Future<bool> updateCategory(int id, Map<String, dynamic> data) async {
    final response = await http.put(Uri.parse('$baseUrl/categories/$id'), headers: await _getHeaders(), body: jsonEncode(data));
    return response.statusCode == 200;
  }

  Future<bool> deleteCategory(int id) async {
    final response = await http.delete(Uri.parse('$baseUrl/categories/$id'), headers: await _getHeaders());
    return response.statusCode == 200;
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
  }
}
