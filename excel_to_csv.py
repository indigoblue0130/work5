import pandas as pd

# =============================================
# ★ここを変更してください★
# =============================================

# 変換したいExcelファイルのパス
INPUT_EXCEL = r"C:\Users\あなたの名前\Desktop\input.xlsx"

# 保存先のCSVファイルのパス（ファイル名も指定）
OUTPUT_CSV  = r"C:\Users\あなたの名前\Documents\output.csv"

# Excelに複数シートがある場合、何枚目を使うか（0始まり：0=1枚目, 1=2枚目）
SHEET_INDEX = 0

# =============================================
# 処理（基本的に変更不要）
# =============================================

def excel_to_csv(input_path, output_path, sheet_index):
    print(f"読み込み中: {input_path}")
    
    # Excelを読み込む（ヘッダーあり前提）
    df = pd.read_excel(input_path, sheet_name=sheet_index, header=0)
    
    print(f"シート: {sheet_index + 1}枚目　/ {len(df)}行 × {len(df.columns)}列 を読み込みました")
    
    # UTF-8（BOMあり）でCSV保存 ※Excelで開いても文字化けしないようにBOMつき
    df.to_csv(output_path, index=False, encoding="utf-8-sig")
    
    print(f"保存完了: {output_path}")

if __name__ == "__main__":
    excel_to_csv(INPUT_EXCEL, OUTPUT_CSV, SHEET_INDEX)