import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../api/api_service.dart';

// ════════════════════════════════════════════════════════════
//  MAIN DEBTS SCREEN
// ════════════════════════════════════════════════════════════
class DebtsScreen extends StatefulWidget {
  @override
  _DebtsScreenState createState() => _DebtsScreenState();
}

class _DebtsScreenState extends State<DebtsScreen> {
  final apiService = ApiService();
  bool isLoading = true;
  List entries = [];
  double totalReceivablesUsd = 0;
  double totalPayablesUsd = 0;
  double totalInstallmentUsd = 0;
  double totalLoanUsd = 0;
  double netDebtsUsd = 0;

  @override
  void initState() {
    super.initState();
    loadLedger();
  }

  void loadLedger() async {
    final result = await apiService.getLedger();
    setState(() {
      if (result != null) {
        entries = result['entries'] ?? [];
        totalReceivablesUsd =
            double.tryParse(result['total_receivables_usd']?.toString() ?? '0') ?? 0;
        totalPayablesUsd =
            double.tryParse(result['total_payables_usd']?.toString() ?? '0') ?? 0;
        totalInstallmentUsd =
            double.tryParse(result['total_installment_usd']?.toString() ?? '0') ?? 0;
        totalLoanUsd =
            double.tryParse(result['total_loan_usd']?.toString() ?? '0') ?? 0;
        netDebtsUsd =
            double.tryParse(result['net_debts_usd']?.toString() ?? '0') ?? 0;
      }
      isLoading = false;
    });
  }

  // ── Open detail screen per person ──
  void _openEntry(Map entry) async {
    final refreshed = await Get.to(() => DebtDetailScreen(entry: entry), transition: Transition.rightToLeft);
    if (refreshed == true) loadLedger();
  }

  // ── Add new debt bottom sheet ──
  void _showAddDebtSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => AddDebtSheet(onSaved: () {
        Navigator.pop(context);
        loadLedger();
      }),
    );
  }

  @override
  Widget build(BuildContext context) {
    final fmt = NumberFormat('#,##0.00');

    // Group entries by status
    final overdue = entries.where((e) => e['status'] == 'overdue').toList();
    final active = entries.where((e) => e['status'] == 'active').toList();
    final partial = entries.where((e) => e['status'] == 'partial').toList();
    final settled = entries.where((e) => e['status'] == 'settled').toList();

    return Scaffold(
      backgroundColor: Color(0xFFF8FAFC),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: Color(0xFF4F46E5)))
          : RefreshIndicator(
              onRefresh: () async => loadLedger(),
              color: Color(0xFF4F46E5),
              child: CustomScrollView(
                physics: BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
                slivers: [
                  // ── App Bar ──
                  SliverAppBar(
                    expandedHeight: 0,
                    pinned: true,
                    backgroundColor: Colors.white,
                    elevation: 0,
                    surfaceTintColor: Colors.transparent,
                    title: Column(
                      children: [
                        Text('الديوان 📒',
                            style: GoogleFonts.almarai(
                                fontWeight: FontWeight.w900,
                                color: Color(0xFF0F172A),
                                fontSize: 18)),
                        Text('تتبع ديونك ومدائنك وأقساطك',
                            style: GoogleFonts.almarai(
                                color: Color(0xFF94A3B8), fontSize: 10, fontWeight: FontWeight.w700)),
                      ],
                    ),
                    centerTitle: true,
                    bottom: PreferredSize(
                        preferredSize: Size.fromHeight(1),
                        child: Divider(height: 1, color: Color(0xFFE2E8F0))),
                  ),

                  SliverPadding(
                    padding: EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        // ── Stats Grid ──
                        GridView.count(
                          shrinkWrap: true,
                          physics: NeverScrollableScrollPhysics(),
                          crossAxisCount: 2,
                          mainAxisSpacing: 12,
                          crossAxisSpacing: 12,
                          childAspectRatio: 1.6,
                          children: [
                            _statCard('💸', 'مديني (لي عندهم)',
                                '\$${fmt.format(totalReceivablesUsd)}',
                                Color(0xFFECFDF5), Color(0xFFD1FAE5), Color(0xFF059669)),
                            _statCard('🏦', 'أنا المدين (عليّ)',
                                '\$${fmt.format(totalPayablesUsd)}',
                                Color(0xFFFEF2F2), Color(0xFFFEE2E2), Color(0xFFDC2626)),
                            _statCard('🛒', 'أقساط شراء',
                                '\$${fmt.format(totalInstallmentUsd)}',
                                Color(0xFFFFFBEB), Color(0xFFFEF3C7), Color(0xFFD97706)),
                            _statCard('📋', 'قروض',
                                '\$${fmt.format(totalLoanUsd)}',
                                Color(0xFFF5F3FF), Color(0xFFEDE9FE), Color(0xFF7C3AED)),
                          ],
                        ),
                        SizedBox(height: 24),

                        // ── Net Balance Card ──
                        _buildNetCard(fmt),
                        SizedBox(height: 24),

                        // ── Entries ──
                        if (entries.isEmpty)
                          _buildEmptyState()
                        else ...[
                          _sectionGroup('متأخرة ⚠️', overdue, Color(0xFFFEE2E2), Color(0xFFDC2626), fmt),
                          _sectionGroup('نشطة', active, Color(0xFFF8FAFC), Color(0xFF4F46E5), fmt),
                          _sectionGroup('مدفوعة جزئياً', partial, Color(0xFFFFFBEB), Color(0xFFD97706), fmt),
                          _sectionGroup('مسدّدة ✅', settled, Color(0xFFF1F5F9), Color(0xFF64748B), fmt),
                        ],
                        SizedBox(height: 100),
                      ]),
                    ),
                  ),
                ],
              ),
            ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showAddDebtSheet,
        backgroundColor: Color(0xFF4F46E5),
        icon: Icon(Icons.add_rounded, color: Colors.white),
        label: Text('إضافة قيد',
            style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 13)),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        elevation: 6,
      ),
    );
  }

  Widget _statCard(String emoji, String label, String value, Color bg, Color border, Color textColor) {
    return Container(
      padding: EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: border, width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(children: [
            Text(emoji, style: TextStyle(fontSize: 16)),
            SizedBox(width: 6),
            Expanded(
              child: Text(label,
                  style: GoogleFonts.almarai(
                      color: textColor.withOpacity(0.8),
                      fontSize: 9,
                      fontWeight: FontWeight.w900),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis),
            ),
          ]),
          Text(value,
              style: GoogleFonts.almarai(
                  color: textColor, fontSize: 16, fontWeight: FontWeight.w900)),
        ],
      ),
    );
  }

  Widget _buildNetCard(NumberFormat fmt) {
    final isNeg = netDebtsUsd < 0;
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 22, vertical: 18),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0F172A), Color(0xFF1E1B4B)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(26),
        boxShadow: [BoxShadow(color: Color(0xFF1E1B4B).withOpacity(0.2), blurRadius: 20, offset: Offset(0, 8))],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('صافي الرصيد المستحق',
                  style: GoogleFonts.almarai(
                      color: Colors.white.withOpacity(0.55), fontSize: 10, fontWeight: FontWeight.bold)),
              SizedBox(height: 6),
              Text(
                '${isNeg ? '-' : '+'}\$${fmt.format(netDebtsUsd.abs())}',
                style: GoogleFonts.almarai(
                    color: isNeg ? Color(0xFFFCA5A5) : Color(0xFF34D399),
                    fontSize: 24,
                    fontWeight: FontWeight.w900),
              ),
            ],
          ),
          Container(
            padding: EdgeInsets.all(12),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.07), shape: BoxShape.circle),
            child: Icon(Icons.balance_rounded, color: Colors.white, size: 22),
          ),
        ],
      ),
    );
  }

  Widget _sectionGroup(String label, List items, Color bg, Color accent, NumberFormat fmt) {
    if (items.isEmpty) return SizedBox.shrink();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(children: [
          Container(width: 3, height: 14, decoration: BoxDecoration(color: accent, borderRadius: BorderRadius.circular(2))),
          SizedBox(width: 8),
          Text(label,
              style: GoogleFonts.almarai(
                  fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF64748B))),
          SizedBox(width: 6),
          Text('(${items.length})',
              style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
        ]),
        SizedBox(height: 10),
        ...items.map((e) => _entryCard(e, fmt)).toList(),
        SizedBox(height: 20),
      ],
    );
  }

  Widget _entryCard(Map e, NumberFormat fmt) {
    final remaining = double.tryParse(e['remaining_amount']?.toString() ?? '0') ?? 0;
    final total = double.tryParse(e['total_amount']?.toString() ?? '0') ?? 0;
    final paid = double.tryParse(e['paid_amount']?.toString() ?? '0') ?? 0;
    final progress = total > 0 ? (paid / total).clamp(0.0, 1.0) : 0.0;
    final currency = e['currency'] ?? 'USD';
    final isSettled = e['status'] == 'settled';

    Color typeColor, typeBg;
    String typeEmoji;
    switch (e['type']) {
      case 'receivable':
        typeColor = Color(0xFF059669); typeBg = Color(0xFFECFDF5); typeEmoji = '💸'; break;
      case 'payable':
        typeColor = Color(0xFFDC2626); typeBg = Color(0xFFFEF2F2); typeEmoji = '🏦'; break;
      case 'installment':
        typeColor = Color(0xFFD97706); typeBg = Color(0xFFFFFBEB); typeEmoji = '🛒'; break;
      default:
        typeColor = Color(0xFF7C3AED); typeBg = Color(0xFFF5F3FF); typeEmoji = '📋';
    }

    return GestureDetector(
      onTap: () => _openEntry(Map<String, dynamic>.from(e)),
      child: Container(
        margin: EdgeInsets.only(bottom: 12),
        padding: EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: Color(0xFFE2E8F0)),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 8, offset: Offset(0, 3))],
        ),
        child: Column(
          children: [
            Row(
              children: [
                // Emoji avatar
                Container(
                  width: 44, height: 44,
                  decoration: BoxDecoration(color: typeBg, borderRadius: BorderRadius.circular(14)),
                  child: Center(child: Text(typeEmoji, style: TextStyle(fontSize: 20))),
                ),
                SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(e['party_name'] ?? 'بدون اسم',
                          style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                      if ((e['description'] ?? '').toString().isNotEmpty)
                        Text(e['description'].toString(),
                            style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold),
                            maxLines: 1, overflow: TextOverflow.ellipsis),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('${fmt.format(remaining)} $currency',
                        style: GoogleFonts.almarai(
                            fontSize: 14, fontWeight: FontWeight.w900,
                            color: isSettled ? Color(0xFF94A3B8) : typeColor,
                            decoration: isSettled ? TextDecoration.lineThrough : null)),
                    _statusPill(e),
                  ],
                ),
              ],
            ),
            SizedBox(height: 12),
            // Progress bar
            ClipRRect(
              borderRadius: BorderRadius.circular(6),
              child: LinearProgressIndicator(
                value: progress,
                backgroundColor: Color(0xFFF1F5F9),
                color: typeColor,
                minHeight: 5,
              ),
            ),
            SizedBox(height: 6),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('تم: ${fmt.format(paid)} $currency',
                    style: GoogleFonts.almarai(fontSize: 9, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                Row(children: [
                  Text('${(progress * 100).toStringAsFixed(0)}%',
                      style: GoogleFonts.almarai(fontSize: 9, color: typeColor, fontWeight: FontWeight.w900)),
                  SizedBox(width: 6),
                  Icon(Icons.arrow_forward_ios_rounded, size: 9, color: Color(0xFFCBD5E1)),
                ]),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _statusPill(Map e) {
    if (e['status'] == 'settled') {
      return _pill('مسدّد ✓', Color(0xFFF1F5F9), Color(0xFF64748B));
    }
    final days = e['days_left'];
    if (days == null) return SizedBox.shrink();
    if (days < 0) return _pill('متأخر ${days.abs()}ي', Color(0xFFFEE2E2), Color(0xFFDC2626));
    if (days == 0) return _pill('اليوم ⚠️', Color(0xFFFEF3C7), Color(0xFFD97706));
    return _pill('$days يوم', Color(0xFFF1F5F9), Color(0xFF64748B));
  }

  Widget _pill(String text, Color bg, Color color) {
    return Container(
      margin: EdgeInsets.only(top: 3),
      padding: EdgeInsets.symmetric(horizontal: 7, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text(text, style: GoogleFonts.almarai(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: EdgeInsets.symmetric(vertical: 50),
        child: Column(
          children: [
            Container(
              padding: EdgeInsets.all(28),
              decoration: BoxDecoration(color: Color(0xFFEEF2FF), shape: BoxShape.circle),
              child: Text('📒', style: TextStyle(fontSize: 50)),
            ),
            SizedBox(height: 20),
            Text('الديوان فارغ', style: GoogleFonts.almarai(fontSize: 16, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
            SizedBox(height: 8),
            Text('أضف أول قيد لمتابعة ديونك ومدائنك',
                style: GoogleFonts.almarai(fontSize: 12, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
            SizedBox(height: 20),
            TextButton.icon(
              onPressed: _showAddDebtSheet,
              icon: Icon(Icons.add_rounded, color: Color(0xFF4F46E5), size: 18),
              label: Text('إضافة قيد جديد',
                  style: GoogleFonts.almarai(color: Color(0xFF4F46E5), fontWeight: FontWeight.w900, fontSize: 13)),
            ),
          ],
        ),
      ),
    );
  }
}

// ════════════════════════════════════════════════════════════
//  DEBT DETAIL SCREEN (per-person page)
// ════════════════════════════════════════════════════════════
class DebtDetailScreen extends StatefulWidget {
  final Map entry;
  DebtDetailScreen({required this.entry});

  @override
  _DebtDetailScreenState createState() => _DebtDetailScreenState();
}

class _DebtDetailScreenState extends State<DebtDetailScreen> {
  final apiService = ApiService();
  late Map entry;
  bool isLoading = false;
  bool _needsRefresh = false;

  @override
  void initState() {
    super.initState();
    entry = Map<String, dynamic>.from(widget.entry);
  }

  Color get typeColor {
    switch (entry['type']) {
      case 'receivable': return Color(0xFF059669);
      case 'payable': return Color(0xFFDC2626);
      case 'installment': return Color(0xFFD97706);
      default: return Color(0xFF7C3AED);
    }
  }

  Color get typeBg {
    switch (entry['type']) {
      case 'receivable': return Color(0xFFECFDF5);
      case 'payable': return Color(0xFFFEF2F2);
      case 'installment': return Color(0xFFFFFBEB);
      default: return Color(0xFFF5F3FF);
    }
  }

  String get typeEmoji {
    switch (entry['type']) {
      case 'receivable': return '💸';
      case 'payable': return '🏦';
      case 'installment': return '🛒';
      default: return '📋';
    }
  }

  void _showPaymentSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => AddPaymentSheet(
        entry: entry,
        onSaved: () async {
          Navigator.pop(context);
          setState(() => isLoading = true);
          final result = await apiService.getLedger();
          if (result != null) {
            final updated = (result['entries'] as List?)
                ?.firstWhere((e) => e['id'] == entry['id'], orElse: () => null);
            if (updated != null) setState(() { entry = Map<String, dynamic>.from(updated); });
          }
          setState(() { isLoading = false; _needsRefresh = true; });
        },
      ),
    );
  }

  void _deleteEntry() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text('حذف القيد', textAlign: TextAlign.center,
            style: GoogleFonts.almarai(fontWeight: FontWeight.w900)),
        content: Text('هل أنت متأكد من حذف قيد "${entry['party_name']}" نهائياً؟',
            textAlign: TextAlign.center,
            style: GoogleFonts.almarai(fontSize: 13, height: 1.6, color: Color(0xFF64748B))),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: Text('إلغاء', style: GoogleFonts.almarai())),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text('حذف', style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.w900)),
          ),
        ],
      ),
    );
    if (confirmed == true) {
      final ok = await apiService.deleteLedger(entry['id']);
      if (ok) {
        Get.back(result: true);
        Get.snackbar('تم الحذف', 'تم حذف القيد بنجاح',
            backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final fmt = NumberFormat('#,##0.00');
    final total = double.tryParse(entry['total_amount']?.toString() ?? '0') ?? 0;
    final paid = double.tryParse(entry['paid_amount']?.toString() ?? '0') ?? 0;
    final remaining = double.tryParse(entry['remaining_amount']?.toString() ?? '0') ?? 0;
    final progress = total > 0 ? (paid / total).clamp(0.0, 1.0) : 0.0;
    final currency = entry['currency'] ?? 'USD';
    final isSettled = entry['status'] == 'settled';
    final payments = entry['payments'] as List? ?? [];

    return WillPopScope(
      onWillPop: () async { Get.back(result: _needsRefresh); return false; },
      child: Scaffold(
        backgroundColor: Color(0xFFF8FAFC),
        body: isLoading
            ? Center(child: CircularProgressIndicator(color: typeColor))
            : CustomScrollView(
                physics: BouncingScrollPhysics(),
                slivers: [
                  // ── Hero App Bar ──
                  SliverAppBar(
                    expandedHeight: 220,
                    pinned: true,
                    backgroundColor: Color(0xFF0F172A),
                    leading: IconButton(
                      icon: Icon(Icons.arrow_back_ios_rounded, color: Colors.white, size: 20),
                      onPressed: () => Get.back(result: _needsRefresh),
                    ),
                    flexibleSpace: FlexibleSpaceBar(
                      background: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [Color(0xFF0F172A), typeColor.withOpacity(0.85)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                        ),
                        child: SafeArea(
                          child: Padding(
                            padding: EdgeInsets.fromLTRB(20, 50, 20, 20),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(children: [
                                  Container(
                                    width: 50, height: 50,
                                    decoration: BoxDecoration(color: Colors.white.withOpacity(0.12), borderRadius: BorderRadius.circular(16)),
                                    child: Center(child: Text(typeEmoji, style: TextStyle(fontSize: 24))),
                                  ),
                                  SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(entry['party_name'] ?? '',
                                            style: GoogleFonts.almarai(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900)),
                                        Text(entry['type_label'] ?? '',
                                            style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.6), fontSize: 11, fontWeight: FontWeight.bold)),
                                      ],
                                    ),
                                  ),
                                  if (!isSettled)
                                    Container(
                                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                      decoration: BoxDecoration(
                                        color: Colors.white.withOpacity(0.12),
                                        borderRadius: BorderRadius.circular(10),
                                        border: Border.all(color: Colors.white.withOpacity(0.2)),
                                      ),
                                      child: Text(
                                        (entry['days_left'] ?? 0) < 0
                                            ? 'متأخر ⚠️'
                                            : '${entry['days_left']} يوم',
                                        style: GoogleFonts.almarai(
                                            color: (entry['days_left'] ?? 0) < 0 ? Color(0xFFFCA5A5) : Colors.white,
                                            fontSize: 10,
                                            fontWeight: FontWeight.w900),
                                      ),
                                    ),
                                ]),
                                SizedBox(height: 20),
                                Row(
                                  children: [
                                    _heroStat('المتبقي', '${fmt.format(remaining)} $currency', Colors.white),
                                    Container(width: 1, height: 36, color: Colors.white.withOpacity(0.15), margin: EdgeInsets.symmetric(horizontal: 16)),
                                    _heroStat('المدفوع', '${fmt.format(paid)} $currency', Color(0xFF34D399)),
                                    Container(width: 1, height: 36, color: Colors.white.withOpacity(0.15), margin: EdgeInsets.symmetric(horizontal: 16)),
                                    _heroStat('الإجمالي', '${fmt.format(total)} $currency', Colors.white.withOpacity(0.6)),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),

                  SliverPadding(
                    padding: EdgeInsets.all(20),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        // Progress
                        Container(
                          padding: EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(22),
                            border: Border.all(color: Color(0xFFE2E8F0)),
                          ),
                          child: Column(
                            children: [
                              Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                                Text('نسبة السداد',
                                    style: GoogleFonts.almarai(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF64748B))),
                                Text('${(progress * 100).toStringAsFixed(1)}%',
                                    style: GoogleFonts.almarai(fontSize: 14, fontWeight: FontWeight.w900, color: typeColor)),
                              ]),
                              SizedBox(height: 10),
                              ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: LinearProgressIndicator(
                                    value: progress, backgroundColor: Color(0xFFF1F5F9), color: typeColor, minHeight: 10),
                              ),
                              if (isSettled) ...[
                                SizedBox(height: 10),
                                Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                                  Icon(Icons.check_circle_rounded, color: Color(0xFF10B981), size: 16),
                                  SizedBox(width: 6),
                                  Text('تم السداد بالكامل ✓',
                                      style: GoogleFonts.almarai(color: Color(0xFF10B981), fontWeight: FontWeight.w900, fontSize: 12)),
                                ]),
                              ],
                            ],
                          ),
                        ),
                        SizedBox(height: 16),

                        // Info Card
                        Container(
                          padding: EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(22),
                            border: Border.all(color: Color(0xFFE2E8F0)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('التفاصيل',
                                  style: GoogleFonts.almarai(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF64748B))),
                              SizedBox(height: 12),
                              if ((entry['description'] ?? '').toString().isNotEmpty)
                                _infoRow(Icons.notes_rounded, 'الوصف', entry['description'].toString()),
                              if ((entry['party_phone'] ?? '').toString().isNotEmpty)
                                _infoRow(Icons.phone_outlined, 'الهاتف', entry['party_phone'].toString()),
                              if (entry['due_date'] != null)
                                _infoRow(Icons.calendar_month_outlined, 'الاستحقاق', entry['due_date'].toString()),
                              if (entry['start_date'] != null)
                                _infoRow(Icons.play_circle_outline_rounded, 'تاريخ البدء', entry['start_date'].toString()),
                              if (entry['installment_count'] != null)
                                _infoRow(Icons.format_list_numbered_rounded, 'عدد الأقساط', '${entry['installment_count']} قسط × ${fmt.format(double.tryParse(entry['installment_amount']?.toString() ?? '0') ?? 0)} $currency'),
                            ],
                          ),
                        ),
                        SizedBox(height: 16),

                        // Payment History
                        if (payments.isNotEmpty) ...[
                          Container(
                            padding: EdgeInsets.all(18),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(22),
                              border: Border.all(color: Color(0xFFE2E8F0)),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('سجل الدفعات (${payments.length})',
                                    style: GoogleFonts.almarai(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF64748B))),
                                SizedBox(height: 12),
                                ...payments.map((p) => _paymentRow(p, fmt, currency)).toList(),
                              ],
                            ),
                          ),
                          SizedBox(height: 16),
                        ],

                        // Action Buttons
                        if (!isSettled)
                          ElevatedButton.icon(
                            onPressed: _showPaymentSheet,
                            icon: Icon(Icons.payment_rounded, size: 18),
                            label: Text('تسجيل دفعة سداد',
                                style: GoogleFonts.almarai(fontWeight: FontWeight.w900, fontSize: 14)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: typeColor,
                              foregroundColor: Colors.white,
                              minimumSize: Size(double.infinity, 54),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                              elevation: 3,
                              shadowColor: typeColor.withOpacity(0.3),
                            ),
                          ),
                        SizedBox(height: 10),
                        OutlinedButton.icon(
                          onPressed: _deleteEntry,
                          icon: Icon(Icons.delete_outline_rounded, size: 16, color: Colors.red),
                          label: Text('حذف هذا القيد',
                              style: GoogleFonts.almarai(color: Colors.red, fontWeight: FontWeight.w900, fontSize: 13)),
                          style: OutlinedButton.styleFrom(
                            minimumSize: Size(double.infinity, 46),
                            side: BorderSide(color: Colors.red.shade200),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          ),
                        ),
                        SizedBox(height: 40),
                      ]),
                    ),
                  ),
                ],
              ),
      ),
    );
  }

  Widget _heroStat(String label, String value, Color color) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: GoogleFonts.almarai(color: Colors.white.withOpacity(0.5), fontSize: 9, fontWeight: FontWeight.bold)),
          SizedBox(height: 3),
          Text(value, style: GoogleFonts.almarai(color: color, fontSize: 11, fontWeight: FontWeight.w900), maxLines: 1, overflow: TextOverflow.ellipsis),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Padding(
      padding: EdgeInsets.only(bottom: 10),
      child: Row(
        children: [
          Icon(icon, size: 16, color: Color(0xFF94A3B8)),
          SizedBox(width: 10),
          Text('$label: ', style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
          Expanded(child: Text(value, style: GoogleFonts.almarai(fontSize: 12, color: Color(0xFF0F172A), fontWeight: FontWeight.w800))),
        ],
      ),
    );
  }

  Widget _paymentRow(Map p, NumberFormat fmt, String currency) {
    final amt = double.tryParse(p['amount']?.toString() ?? '0') ?? 0;
    return Container(
      margin: EdgeInsets.only(bottom: 8),
      padding: EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(color: Color(0xFFF8FAFC), borderRadius: BorderRadius.circular(14)),
      child: Row(
        children: [
          Container(
            width: 8, height: 8,
            decoration: BoxDecoration(color: Color(0xFF10B981), shape: BoxShape.circle),
          ),
          SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('${fmt.format(amt)} $currency',
                    style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF10B981))),
                if ((p['notes'] ?? '').toString().isNotEmpty)
                  Text(p['notes'].toString(),
                      style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
              ],
            ),
          ),
          Text(p['payment_date']?.toString() ?? '',
              style: GoogleFonts.almarai(fontSize: 10, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}

// ════════════════════════════════════════════════════════════
//  ADD DEBT BOTTOM SHEET
// ════════════════════════════════════════════════════════════
class AddDebtSheet extends StatefulWidget {
  final VoidCallback onSaved;
  AddDebtSheet({required this.onSaved});

  @override
  _AddDebtSheetState createState() => _AddDebtSheetState();
}

class _AddDebtSheetState extends State<AddDebtSheet> {
  final apiService = ApiService();
  final partyNameCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  final amountCtrl = TextEditingController();
  final descCtrl = TextEditingController();
  final notesCtrl = TextEditingController();
  final installCountCtrl = TextEditingController();
  final installAmtCtrl = TextEditingController();

  String type = 'receivable';
  String currency = 'USD';
  DateTime startDate = DateTime.now();
  DateTime dueDate = DateTime.now().add(Duration(days: 30));
  bool isSaving = false;

  final types = [
    {'value': 'receivable', 'label': 'مديني (لي عندهم)', 'emoji': '💸', 'color': Color(0xFF059669), 'bg': Color(0xFFECFDF5), 'border': Color(0xFFD1FAE5), 'hint': '✅ أنت الدائن — الطرف الآخر مدين لك'},
    {'value': 'payable', 'label': 'أنا المدين (عليّ)', 'emoji': '🏦', 'color': Color(0xFFDC2626), 'bg': Color(0xFFFEF2F2), 'border': Color(0xFFFEE2E2), 'hint': '⚠️ أنت المدين — عليك سداد هذا المبلغ'},
    {'value': 'installment', 'label': 'تقسيط شراء', 'emoji': '🛒', 'color': Color(0xFFD97706), 'bg': Color(0xFFFFFBEB), 'border': Color(0xFFFEF3C7), 'hint': '🛒 شراء بالتقسيط — حدد المبلغ وعدد الأقساط'},
    {'value': 'loan', 'label': 'قرض', 'emoji': '📋', 'color': Color(0xFF7C3AED), 'bg': Color(0xFFF5F3FF), 'border': Color(0xFFEDE9FE), 'hint': '📋 قرض — حدد إذا كنت المقرِض أو المقترِض'},
  ];

  Map get currentType => types.firstWhere((t) => t['value'] == type);

  Future<void> _pickDate(bool isDue) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: isDue ? dueDate : startDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2035),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: ColorScheme.light(primary: currentType['color'] as Color, onPrimary: Colors.white),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => isDue ? dueDate = picked : startDate = picked);
  }

  void _save() async {
    if (partyNameCtrl.text.isEmpty || amountCtrl.text.isEmpty) {
      Get.snackbar('حقول ناقصة', 'يرجى إدخال اسم الطرف والمبلغ',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
      return;
    }
    setState(() => isSaving = true);
    final data = {
      'type': type,
      'party_name': partyNameCtrl.text,
      'party_phone': phoneCtrl.text,
      'total_amount': amountCtrl.text,
      'currency': currency,
      'description': descCtrl.text,
      'notes': notesCtrl.text,
      'start_date': DateFormat('yyyy-MM-dd').format(startDate),
      'due_date': DateFormat('yyyy-MM-dd').format(dueDate),
      if (type == 'installment') 'installment_count': installCountCtrl.text,
      if (type == 'installment') 'installment_amount': installAmtCtrl.text,
    };
    final ok = await apiService.addLedger(data);
    setState(() => isSaving = false);
    if (ok) {
      widget.onSaved();
      Get.snackbar('تم التسجيل ✅', 'تم تسجيل القيد في الديوان',
          backgroundColor: Colors.green.shade50, colorText: Colors.green.shade800);
    } else {
      Get.snackbar('خطأ', 'فشل تسجيل القيد، يرجى المحاولة',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
    }
  }

  @override
  Widget build(BuildContext context) {
    final Color accent = currentType['color'] as Color;
    final Color accentBg = currentType['bg'] as Color;

    return Container(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
      ),
      child: SingleChildScrollView(
        physics: BouncingScrollPhysics(),
        padding: EdgeInsets.fromLTRB(22, 0, 22, 30),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Handle
            Center(
              child: Container(
                margin: EdgeInsets.symmetric(vertical: 14),
                width: 44, height: 4,
                decoration: BoxDecoration(color: Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(10)),
              ),
            ),
            Text('إضافة قيد جديد',
                textAlign: TextAlign.center,
                style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
            SizedBox(height: 20),

            // ── Type Selector (2×2 Grid) ──
            GridView.count(
              shrinkWrap: true,
              physics: NeverScrollableScrollPhysics(),
              crossAxisCount: 2,
              mainAxisSpacing: 10,
              crossAxisSpacing: 10,
              childAspectRatio: 2.5,
              children: types.map((t) {
                final selected = type == t['value'];
                return GestureDetector(
                  onTap: () => setState(() => type = t['value'] as String),
                  child: AnimatedContainer(
                    duration: Duration(milliseconds: 180),
                    padding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    decoration: BoxDecoration(
                      color: selected ? t['bg'] as Color : Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: selected ? t['border'] as Color : Color(0xFFE2E8F0),
                        width: selected ? 2 : 1.5,
                      ),
                    ),
                    child: Row(
                      children: [
                        Text(t['emoji'] as String, style: TextStyle(fontSize: 16)),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text(t['label'] as String,
                              style: GoogleFonts.almarai(
                                  color: selected ? t['color'] as Color : Color(0xFF64748B),
                                  fontWeight: FontWeight.w900, fontSize: 11),
                              maxLines: 1, overflow: TextOverflow.ellipsis),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),

            // Hint
            SizedBox(height: 10),
            Container(
              padding: EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              decoration: BoxDecoration(color: accentBg, borderRadius: BorderRadius.circular(14)),
              child: Text(currentType['hint'] as String,
                  style: GoogleFonts.almarai(color: accent, fontSize: 11, fontWeight: FontWeight.bold)),
            ),
            SizedBox(height: 18),

            // ── Fields ──
            _field(partyNameCtrl, 'اسم الطرف الآخر *',
                icon: Icons.person_outline_rounded,
                hint: type == 'receivable' ? 'اسم المدين...' : type == 'payable' ? 'اسم الدائن...' : 'الجهة / الشركة...',
                accent: accent),
            SizedBox(height: 12),
            Row(children: [
              Expanded(child: _field(phoneCtrl, 'الهاتف (اختياري)', icon: Icons.phone_outlined, accent: accent, keyboard: TextInputType.phone)),
              SizedBox(width: 12),
              Expanded(child: _currencyDropdown(accent)),
            ]),
            SizedBox(height: 12),
            _field(amountCtrl, 'المبلغ الإجمالي *', icon: Icons.monetization_on_outlined, accent: accent, keyboard: TextInputType.numberWithOptions(decimal: true)),
            SizedBox(height: 12),
            _field(descCtrl, 'الوصف / السبب', icon: Icons.notes_rounded, hint: 'مثلاً: قرض للمشروع، شراء سيارة...', accent: accent),
            SizedBox(height: 12),

            // Installment fields
            if (type == 'installment') ...[
              Container(
                padding: EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Color(0xFFFFFBEB),
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: Color(0xFFFEF3C7)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('تفاصيل التقسيط',
                        style: GoogleFonts.almarai(fontSize: 10, fontWeight: FontWeight.w900, color: Color(0xFFD97706))),
                    SizedBox(height: 10),
                    Row(children: [
                      Expanded(child: _field(installCountCtrl, 'عدد الأقساط', accent: Color(0xFFD97706), keyboard: TextInputType.number)),
                      SizedBox(width: 12),
                      Expanded(child: _field(installAmtCtrl, 'قيمة القسط', accent: Color(0xFFD97706), keyboard: TextInputType.numberWithOptions(decimal: true))),
                    ]),
                  ],
                ),
              ),
              SizedBox(height: 12),
            ],

            // Dates
            Row(children: [
              Expanded(child: _datePicker('تاريخ البدء', startDate, accent, false)),
              SizedBox(width: 12),
              Expanded(child: _datePicker('الاستحقاق', dueDate, accent, true)),
            ]),
            SizedBox(height: 12),
            _field(notesCtrl, 'ملاحظات إضافية', icon: Icons.edit_note_rounded, accent: accent),
            SizedBox(height: 22),

            // Save button
            ElevatedButton(
              onPressed: isSaving ? null : _save,
              style: ElevatedButton.styleFrom(
                backgroundColor: accent,
                minimumSize: Size(double.infinity, 54),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                elevation: 3,
                shadowColor: accent.withOpacity(0.3),
              ),
              child: isSaving
                  ? SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                  : Text('✓ حفظ القيد',
                      style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _field(TextEditingController ctrl, String label,
      {IconData? icon, String? hint, required Color accent, TextInputType keyboard = TextInputType.text}) {
    return TextField(
      controller: ctrl,
      keyboardType: keyboard,
      style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        labelStyle: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
        hintStyle: GoogleFonts.almarai(color: Color(0xFFCBD5E1), fontSize: 12),
        prefixIcon: icon != null ? Icon(icon, color: accent, size: 18) : null,
        filled: true,
        fillColor: Color(0xFFF8FAFC),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: accent, width: 1.5)),
        contentPadding: EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
    );
  }

  Widget _currencyDropdown(Color accent) {
    return DropdownButtonFormField<String>(
      value: currency,
      dropdownColor: Colors.white,
      borderRadius: BorderRadius.circular(18),
      style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
      decoration: InputDecoration(
        labelText: 'العملة',
        labelStyle: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
        filled: true,
        fillColor: Color(0xFFF8FAFC),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: accent, width: 1.5)),
        contentPadding: EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
      items: ['USD', 'SYP', 'EUR', 'TRY', 'SAR']
          .map((c) => DropdownMenuItem(value: c, child: Text(c)))
          .toList(),
      onChanged: (v) => setState(() => currency = v!),
    );
  }

  Widget _datePicker(String label, DateTime date, Color accent, bool isDue) {
    return GestureDetector(
      onTap: () => _pickDate(isDue),
      child: Container(
        padding: EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        decoration: BoxDecoration(
          color: Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Color(0xFFE2E8F0)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: GoogleFonts.almarai(fontSize: 9, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
            SizedBox(height: 4),
            Row(children: [
              Icon(Icons.calendar_month_outlined, color: accent, size: 14),
              SizedBox(width: 6),
              Text(DateFormat('yyyy-MM-dd').format(date),
                  style: GoogleFonts.almarai(fontSize: 12, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
            ]),
          ],
        ),
      ),
    );
  }
}

// ════════════════════════════════════════════════════════════
//  ADD PAYMENT BOTTOM SHEET
// ════════════════════════════════════════════════════════════
class AddPaymentSheet extends StatefulWidget {
  final Map entry;
  final VoidCallback onSaved;
  AddPaymentSheet({required this.entry, required this.onSaved});

  @override
  _AddPaymentSheetState createState() => _AddPaymentSheetState();
}

class _AddPaymentSheetState extends State<AddPaymentSheet> {
  final apiService = ApiService();
  late TextEditingController amountCtrl;
  final notesCtrl = TextEditingController();
  DateTime payDate = DateTime.now();
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    amountCtrl = TextEditingController(text: widget.entry['remaining_amount']?.toString() ?? '');
  }

  Color get typeColor {
    switch (widget.entry['type']) {
      case 'receivable': return Color(0xFF059669);
      case 'payable': return Color(0xFFDC2626);
      case 'installment': return Color(0xFFD97706);
      default: return Color(0xFF7C3AED);
    }
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: payDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2035),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: ColorScheme.light(primary: typeColor, onPrimary: Colors.white),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => payDate = picked);
  }

  void _save() async {
    if (amountCtrl.text.isEmpty) {
      Get.snackbar('حقل ناقص', 'يرجى إدخال المبلغ',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
      return;
    }
    setState(() => isSaving = true);
    final ok = await apiService.addLedgerPayment(widget.entry['id'], {
      'amount': amountCtrl.text,
      'notes': notesCtrl.text,
      'payment_date': DateFormat('yyyy-MM-dd').format(payDate),
    });
    setState(() => isSaving = false);
    if (ok) {
      widget.onSaved();
      Get.snackbar('تم التسجيل ✅', 'تم تسجيل الدفعة وتحديث الرصيد',
          backgroundColor: Colors.green.shade50, colorText: Colors.green.shade800);
    } else {
      Get.snackbar('خطأ', 'فشل تسجيل الدفعة',
          backgroundColor: Colors.red.shade50, colorText: Colors.red.shade800);
    }
  }

  @override
  Widget build(BuildContext context) {
    final fmt = NumberFormat('#,##0.00');
    final remaining = double.tryParse(widget.entry['remaining_amount']?.toString() ?? '0') ?? 0;
    final currency = widget.entry['currency'] ?? 'USD';

    return Container(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
      ),
      child: Padding(
        padding: EdgeInsets.fromLTRB(22, 0, 22, 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Container(
                margin: EdgeInsets.symmetric(vertical: 14),
                width: 44, height: 4,
                decoration: BoxDecoration(color: Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(10)),
              ),
            ),
            Text('تسجيل دفعة سداد',
                textAlign: TextAlign.center,
                style: GoogleFonts.almarai(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
            SizedBox(height: 6),
            Text('${widget.entry['party_name']} • المتبقي: ${fmt.format(remaining)} $currency',
                textAlign: TextAlign.center,
                style: GoogleFonts.almarai(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
            SizedBox(height: 20),

            // Amount
            TextField(
              controller: amountCtrl,
              keyboardType: TextInputType.numberWithOptions(decimal: true),
              style: GoogleFonts.almarai(fontSize: 28, fontWeight: FontWeight.w900, color: typeColor),
              decoration: InputDecoration(
                labelText: 'مبلغ الدفعة ($currency)',
                labelStyle: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
                prefixIcon: Icon(Icons.payments_rounded, color: typeColor),
                filled: true,
                fillColor: Color(0xFFF8FAFC),
                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: typeColor, width: 1.5)),
              ),
            ),
            SizedBox(height: 12),

            // Date
            GestureDetector(
              onTap: _pickDate,
              child: Container(
                padding: EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                decoration: BoxDecoration(
                  color: Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Color(0xFFE2E8F0)),
                ),
                child: Row(
                  children: [
                    Icon(Icons.calendar_month_outlined, color: typeColor, size: 18),
                    SizedBox(width: 10),
                    Text(DateFormat('yyyy-MM-dd').format(payDate),
                        style: GoogleFonts.almarai(fontSize: 13, fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                    Spacer(),
                    Text('تغيير', style: GoogleFonts.almarai(fontSize: 11, color: typeColor, fontWeight: FontWeight.w900)),
                  ],
                ),
              ),
            ),
            SizedBox(height: 12),

            // Notes
            TextField(
              controller: notesCtrl,
              style: GoogleFonts.almarai(color: Color(0xFF0F172A), fontSize: 13, fontWeight: FontWeight.w800),
              decoration: InputDecoration(
                labelText: 'ملاحظات (حوالة، نقدي...)',
                labelStyle: GoogleFonts.almarai(color: Color(0xFF94A3B8), fontSize: 11, fontWeight: FontWeight.bold),
                prefixIcon: Icon(Icons.edit_note_rounded, color: typeColor, size: 18),
                filled: true,
                fillColor: Color(0xFFF8FAFC),
                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Color(0xFFE2E8F0))),
                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: typeColor, width: 1.5)),
              ),
            ),
            SizedBox(height: 22),

            ElevatedButton(
              onPressed: isSaving ? null : _save,
              style: ElevatedButton.styleFrom(
                backgroundColor: typeColor,
                minimumSize: Size(double.infinity, 54),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                elevation: 3,
                shadowColor: typeColor.withOpacity(0.3),
              ),
              child: isSaving
                  ? SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                  : Text('تأكيد الدفعة',
                      style: GoogleFonts.almarai(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 15)),
            ),
          ],
        ),
      ),
    );
  }
}
