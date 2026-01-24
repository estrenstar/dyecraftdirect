# 301 REDIRECTS IMPLEMENTATION GUIDE
## DyeCraftDirect SEO Fixes

### Overview
This document explains the 301 redirect implementation that consolidates duplicate product URLs to their canonical versions. This resolves the duplicate content issues identified in the Semrush audit.

**Status:** ✅ One-hop only | No redirect chains | Query strings preserved

---

## Problem
Your OpenCart store has products accessible via multiple URL paths (due to category breadcrumb structures). This creates duplicate content:

```
Example:
❌ https://dyecraftdirect.com/heat-presses/accessories-for-heat-presses/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
❌ https://dyecraftdirect.com/sublimation-blanks/sublimation-mugs-and-drinkware/drinkware-accessories/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
✅ https://dyecraftdirect.com/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7 (canonical)
```

All non-canonical URLs now permanently redirect to the canonical URL.

---

## Solution
**File:** `redirects-301.htaccess`

**Coverage:** 37 canonicalized product URLs with 187 individual 301 redirect rules

**Key Features:**
- ✅ Permanent (301) redirects
- ✅ One-hop only (no chains)
- ✅ Query strings preserved via QSA flag
- ✅ Based on exact CSV mapping provided

---

## Implementation Steps

### Option 1: Merge Into Main .htaccess (RECOMMENDED)
1. Open `/root/.htaccess` (your main .htaccess file)
2. Copy all rules from `redirects-301.htaccess` into the `<IfModule mod_rewrite.c>` section
3. Test redirects at: https://www.redirect-checker.org/

### Option 2: Include Redirect File
Add this to your main `.htaccess`:
```apache
Include redirects-301.htaccess
```

### Option 3: Server-level Configuration (if you have .htaccess disabled)
Contact your hosting provider to implement rules in `httpd.conf` or `apache2.conf`

---

## Redirect Examples

### Mini-Ceramic-Mug-Wrap (9 old URLs → 1 canonical)
```
heat-presses/accessories-for-heat-presses/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
    ↓ 301
mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7

sublimation-blanks/sublimation-mugs-and-drinkware/drinkware-accessories/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
    ↓ 301
mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
[...and 7 more]
```

### Query String Preservation
```
pu-oval-key-chain-black-ya108?manufacturer_id=15&page=3
    ↓ 301 [QSA]
pu-oval-key-chain-black-ya108?manufacturer_id=15&page=3
```

---

## Products Covered

| Product | Old URLs | Canonical URL |
|---------|----------|---------------|
| Mini-Ceramic-Mug-Wrap (3oz) | 9 | `/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7` |
| 11oz Sparkling Pink Mug | 6 | `/11oz-sparkling-pink-mug` |
| Heart Braided Key Chain Red | 4 | `/heart-braided-key-chain-red-ya128h-r-floor-9` |
| PU Oval Key Chain Black | 8 | `/pu-oval-key-chain-black-ya108` |
| Pedestal Hotronix Air Fusion Heat Press | 10 | `/pedestal-hotronix-air-fusion-heat-press-16x20` |

**Total:** 37 unique canonical URLs | 187 redirect rules

---

## Testing Checklist

After implementation, verify:

1. **Basic Redirect Test**
   ```bash
   curl -I https://dyecraftdirect.com/heat-presses/mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
   # Should show: HTTP/1.1 301 Moved Permanently
   # Location: /mini-ceramic-mug-wrap-3oz-klbj-3mk-c-7
   ```

2. **Query String Preservation**
   ```
   old-url?manufacturer_id=15&page=2 
   Should redirect to canonical URL WITH query string preserved
   ```

3. **No Redirect Chains**
   - ❌ A→B→C (bad)
   - ✅ A→C, B→C (good - all point directly to canonical)

4. **Google Search Console**
   - Submit canonical URLs to GSC
   - Monitor "Coverage" report for any remaining duplicates
   - Check "Enhancements" > "Canonicals" for issues

---

## SEO Impact

**Before:**
- ❌ 37 canonicalized pages (marked as non-indexable)
- ❌ Duplicate content across category paths
- ❌ Link equity diluted across variants

**After:**
- ✅ All traffic consolidated to canonical URLs
- ✅ Link equity accumulated on canonical versions
- ✅ Cleaner crawl budget for Googlebot
- ✅ Improved keyword rankings on canonical URLs

---

## Related SEO Improvements (seo-fixes branch)

This is part of a larger SEO fix set. See also:
- [ ] `header.twig` - Add canonical tags in HTML
- [ ] `header.twig` - Add structured data (JSON-LD)
- [ ] `header.twig` - Add Open Graph tags
- [ ] `robots.txt` - Update crawler directives
- [ ] `sitemap.xml` - Ensure only canonical URLs listed

---

## Notes
- ✅ Implemented exactly from CSV mapping provided
- ✅ One-hop only (no chains or loops)
- ✅ Query strings automatically preserved via RewriteFlag QSA
- ✅ Ready for production deployment

---

**Created:** 2026-01-25
**Branch:** `seo-fixes`
**Status:** Ready for review & testing
