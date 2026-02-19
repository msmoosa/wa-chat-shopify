# Migration Guide: Script Tags to Theme App Extensions

## Overview

Shopify has updated their requirements and no longer allows script tags for modifying merchant themes. This app has been migrated to use **Theme App Extensions** instead.

## What Changed

### Before (Script Tags)
- Script tags were automatically injected into the theme
- No merchant control over placement
- Violates Shopify's current app review guidelines

### After (Theme App Extensions)
- Merchants add the block through the theme editor
- Full control over placement and configuration
- Complies with Shopify's requirements

## Migration Steps

### 1. Deploy the Theme App Extension

```bash
# Install Shopify CLI if not already installed
npm install -g @shopify/cli @shopify/theme

# Login to Shopify
shopify auth login

# Deploy the extension
cd extensions/wa-chat-button
shopify app deploy
```

### 2. Remove Script Tag Configuration

The script tag configuration has been removed from `config/shopify-app.php`. The old script tag code is no longer used.

### 3. Merchant Setup (Required)

**Important:** Merchants must manually add the block to their theme:

1. Go to **Online Store > Themes**
2. Click **Customize** on the active theme
3. In the theme editor, look for **App embeds** or **App blocks**
4. Find **"WhatsApp Chat Button"** and add it to the theme
5. Configure the settings:
   - Enable the button
   - Set phone number
   - Choose position and design
   - Configure other settings as needed

### 4. App Settings Integration

The theme extension will automatically fetch configuration from your app's API endpoint (`/external/store?shop=...`). This means:
- Settings configured in your app UI will override theme editor settings
- Merchants can still customize appearance in the theme editor
- The extension falls back to theme editor settings if API is unavailable

## Testing

1. Deploy the extension to your development store
2. Add the block through the theme editor
3. Verify the button appears and functions correctly
4. Test all design types and configurations
5. Verify mobile/desktop visibility settings work

## Rollback Plan

If you need to rollback:
1. The old script tag code is still in `public/js/wa-chat.js`
2. Re-enable script tags in `config/shopify-app.php` (not recommended)
3. Note: This will cause app review to fail

## Support

For merchants having issues:
1. Ensure they've added the block through the theme editor
2. Verify the block is enabled in the block settings
3. Check that phone number is configured
4. Verify API endpoint is accessible

## Next Steps for App Review

1. ✅ Theme app extension created
2. ✅ Script tags removed
3. ⏳ Deploy extension to Shopify
4. ⏳ Test in development store
5. ⏳ Update app listing screenshots if needed
6. ⏳ Resubmit for review
