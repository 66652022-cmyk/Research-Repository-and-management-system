<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Univer Spreadsheet (Preset)</title>

  <!-- Dependencies -->
  <script src="https://unpkg.com/react@18.3.1/umd/react.production.min.js"></script>
  <script src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/rxjs@7.8.1/dist/bundles/rxjs.umd.min.js"></script>
  <script src="https://unpkg.com/echarts@5.6.0/dist/echarts.min.js"></script>

  <!-- Univer Preset -->
  <script src="https://unpkg.com/@univerjs/presets/lib/umd/index.js"></script>
  <script src="https://unpkg.com/@univerjs/preset-sheets-core/lib/umd/index.js"></script>
  <script src="https://unpkg.com/@univerjs/preset-sheets-core/lib/umd/locales/en-US.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="https://unpkg.com/@univerjs/preset-sheets-core/lib/index.css" />

  <style>
    html, body, #app {
      margin: 0;
      padding: 0;
      height: 100%;
    }
  </style>
</head>
<body>
  <div id="app"></div>

  <script>
    const { createUniver } = UniverPresets;
    const { LocaleType, mergeLocales } = UniverCore;
    const { UniverSheetsCorePreset } = UniverPresetSheetsCore;

    const { univerAPI } = createUniver({
      locale: LocaleType.EN_US,
      locales: {
        [LocaleType.EN_US]: mergeLocales(UniverPresetSheetsCoreEnUS),
      },
      presets: [
        UniverSheetsCorePreset({
          container: 'app', // where to render the sheet
        }),
      ],
    });

    // Create a workbook
    univerAPI.createWorkbook({ name: 'Test Sheet' });
  </script>
</body>
</html>
