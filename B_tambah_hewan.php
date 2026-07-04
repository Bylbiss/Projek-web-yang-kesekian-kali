<div class="pet-header">
    <h4 style="margin-bottom: 15px; color: #333;">Hewan Peliharaan</h4>
    <button type="button" class="remove-pet" onclick="this.closest('.pet-item').remove()">×</button>
</div>

<div class="form-group">
    <input type="text" name="nama_pet[]" placeholder="Nama hewan (contoh: Snowy)" required>
</div>

<div class="gender-selector">
    <div class="gender-btn" data-value="jantan">Jantan</div>
    <div class="gender-btn" data-value="betina">Betina</div>
    <div class="gender-btn" data-value="tidak_diketahui">Tidak Diketahui</div>
</div>

<div class="form-group">
    <select name="jenis_hewan[]" required>
        <option value="">Pilih Jenis Hewan</option>
        <option value="kucing">Kucing</option>
        <option value="anjing">Anjing</option>
        <option value="kelinci">Kelinci</option>
        <option value="hamster">Hamster</option>
        <option value="burung">Burung</option>
        <option value="ikan">Ikan</option>
        <option value="musang">Musang</option>
        <option value="kura-kura">Kura-kura</option>
        <option value="landak">Landak
    </div>
        <option value="sapi">Sapi</option>
        <option value="kambing">Kambing</option>
        <option value="domba">Domba</option>
        <option value="ayam">Ayam</option>
        <option value="kuda">Kuda</option>
        <option value="lain-lain">Lain-lain</option>
        </select>
    </div>

<div class="form-group">
    <input type="text" name="ras[]" placeholder="Ras (contoh: Persia, Anggora, Kampung)">
</div>

<div class="input-row">
    <div>
        <input type="date" name="tanggal_lahir[]" placeholder="Tanggal Lahir">
    </div>
    <div>
        <input type="number" name="usia[]" placeholder="Usia (tahun)" min="0" max="50">
    </div>
</div>

<div class="form-group">
    <input type="number" name="berat[]" placeholder="Berat (kg)" step="0.1" min="0" max="100">
</div>

<div class="form-group">
    <select name="sterilisasi[]">
        <option value="belum">Belum Sterilisasi</option>
        <option value="sudah">Sudah Sterilisasi</option>
    </select>
</div>