<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Data Kegiatan Posyandu</title>
    <style>
        @page { margin: 10px; size: landscape; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 4px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 6px; margin-bottom: 2px; }
        .title { text-align: center; font-size: 8px; font-weight: 700; margin-bottom: 4px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th, .main-table td {
            border: 0.5px solid #111827;
            padding: 1px 0.5px;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }
        .main-table th { font-weight: 700; background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="lampiran">BUKU BANTU POKJA IV</div>
    <div class="title">BUKU DATA KEGIATAN POSYANDU</div>

    <table class="main-table">
        <colgroup>
            <col style="width: 15px;"> <!-- 1 -->
            <col style="width: 25px;"> <!-- 2 -->
            <col style="width: 15px;"> <!-- 3 -->
            <col style="width: 15px;"> <!-- 4 -->
            <col style="width: 15px;"> <!-- 5 -->
            <col style="width: 15px;"> <!-- 6 -->
            @for ($i = 0; $i < 44; $i++)
                <col style="width: 12px;"> <!-- 7-50 -->
            @endfor
            <col style="width: 30px;"> <!-- 55/KET -->
        </colgroup>
        <thead>
            <tr>
                <th rowspan="4">1</th>
                <th rowspan="4">2</th>
                <th rowspan="4">3</th>
                <th rowspan="4">4</th>
                <th rowspan="4">5</th>
                <th rowspan="4">6</th>
                <th colspan="8">JUMLAH AKESESOR KB</th>
                <th colspan="12">JUMLAH PENGUNJUNG</th>
                <th colspan="2">IMUNISASI TT IBU HAMIL</th>
                <th colspan="12">JUMLAH BAYI YANG DIIMUNISASI</th>
                <th colspan="6">BALITA MENDRITA DIARE</th>
                <th rowspan="4">55</th>
            </tr>
            <tr>
                <th rowspan="3">7</th>
                <th rowspan="3">8</th>
                <th rowspan="3">9</th>
                <th rowspan="3">10</th>
                <th rowspan="3">11</th>
                <th rowspan="3">12</th>
                <th rowspan="3">13</th>
                <th rowspan="3">14</th>
                <th colspan="2">JMLH BALITA</th>
                <th colspan="2">JMLH KMS</th>
                <th colspan="2">D</th>
                <th colspan="2">N</th>
                <th colspan="2">VIT A</th>
                <th colspan="2">PMT</th>
                <th rowspan="3">27</th>
                <th rowspan="3">28</th>
                <th rowspan="3">29</th>
                <th colspan="3">DPT</th>
                <th colspan="4">POLIO</th>
                <th rowspan="3">37</th>
                <th colspan="3">HEPATITIS B</th>
                <th colspan="3">JUMLAH</th>
                <th rowspan="3">44</th>
                <th colspan="2">L</th>
                <th colspan="2">P</th>
                <th colspan="2">JML</th>
            </tr>
            <tr>
                <th rowspan="2">15</th><th rowspan="2">16</th>
                <th rowspan="2">17</th><th rowspan="2">18</th>
                <th rowspan="2">19</th><th rowspan="2">20</th>
                <th rowspan="2">21</th><th rowspan="2">22</th>
                <th rowspan="2">23</th><th rowspan="2">24</th>
                <th rowspan="2">25</th><th rowspan="2">26</th>
                <th>30</th><th>31</th><th>32</th>
                <th>33</th><th>34</th><th>35</th><th>36</th>
                <th>38</th><th>39</th><th>40</th>
                <th>41</th><th>42</th><th>43</th>
                <th>45</th><th>46</th>
                <th>47</th><th>48</th>
                <th>49</th><th>50</th>
            </tr>
            <tr>
                <th>I</th><th>II</th><th>III</th>
                <th>I</th><th>II</th><th>III</th><th>IV</th>
                <th>I</th><th>II</th><th>III</th>
                <th>L</th><th>P</th><th>JML</th>
                <th>L</th><th>P</th>
                <th>L</th><th>P</th>
                <th>L</th><th>P</th>
            </tr>
            <tr>
                <th>NO</th>
                <th>BULAN</th>
                <th>JML BUMIL</th>
                <th>PERIKSA</th>
                <th>FE TAB</th>
                <th>JML BUSUI</th>
                <th>KDM</th><th>PIL</th><th>IMP</th><th>MOP</th><th>MOW</th><th>IUD</th><th>STK</th><th>DLL</th>
                <th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th>
                <th>I</th><th>II</th>
                <th>BCG</th><th>I</th><th>II</th><th>III</th><th>I</th><th>II</th><th>III</th><th>IV</th><th>CPK</th><th>I</th><th>II</th><th>III</th>
                <th>L</th><th>P</th><th>JML</th>
                <th>ORALIT</th>
                <th>L</th><th>P</th><th>L</th><th>P</th><th>L</th><th>P</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="51">Data belum tersedia. Laporan ini merupakan format ringkasan (51 kolom).</td>
            </tr>
        </tbody>
    </table>
    @include('pdf.partials._report_metadata')
</body>
</html>
