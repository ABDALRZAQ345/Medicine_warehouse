<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class WordServices
{
    public function generateInvoice($order,$user)
    {
        $phpWord = new PhpWord();

        // إعداد بعض الأنماط
        $phpWord->addTitleStyle(1, ['name' => 'Arial', 'size' => 20, 'bold' => true], ['alignment' => 'center']);
        $phpWord->addFontStyle('boldText', ['bold' => true, 'size' => 12]);
        $phpWord->addFontStyle('normalText', ['size' => 12]);

        // إعداد جدول بتنسيق مخصص
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 50
        ];
        $phpWord->addTableStyle('productsTable', $tableStyle);

        // إنشاء قسم جديد في المستند
        $section = $phpWord->addSection();

        // إضافة عنوان الفاتورة
        $section->addTitle('Order from snake pharmacy', 1);

        // إضافة فاصل بين الأقسام
        $section->addTextBreak(1);

        // بيانات العميل
        $section->addText('name  :' . $user->first_name, 'boldText');
        $section->addText('email :' .$user->email, 'normalText');
        $section->addText('date : ' .$order->created_at, 'normalText');

        // فاصل بين الأقسام
        $section->addTextBreak(1);

        // بيانات الطلب
        $section->addText("Order number ".$order->id, 'boldText');
        $section->addText("Products :", 'boldText');

        // إنشاء جدول للمنتجات
        $table = $section->addTable('productsTable');

        // إضافة صف الرأس
        $table->addRow();
        $table->addCell(4000)->addText("scientific_name", 'boldText');
        $table->addCell(2000)->addText("trade_name", 'boldText');
        $table->addCell(2000)->addText('quantity', 'boldText');
        $table->addCell(2000)->addText('price', 'boldText');

        // بيانات المنتجات (مثال)
        $medicines=$order->items;
        foreach ($medicines as $medicine) {
            $table->addRow();
            $table->addCell(4000)->addText($medicine->scientific_name, 'normalText');
            $table->addCell(2000)->addText($medicine->trade_name, 'normalText');
            $table->addCell(2000)->addText($medicine->quantity, 'normalText');
            $table->addCell(2000)->addText($medicine->price, 'normalText');

        }


        // إضافة خط فاصل بين الجدول والمجموع
        $section->addTextBreak(1);

        // المجموع
        $section->addText('total price : ' . $order->total_price , 'boldText');

        // إنشاء ملف وورد
        $fileName = 'order'.$order->id.'docx';
        $filePath = storage_path($fileName);

        // حفظ المستند كملف وورد
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return $filePath;
    }
}
