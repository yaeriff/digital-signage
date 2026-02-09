/* KONFIGURASI & VARIABEL GLOBAL */
const API_URL = "/api/display-content"; // URL API Laravel
const container = document.getElementById("dynamic-content");

// Variabel untuk melacak video apa yang sedang diputar sekarang
let currentVideoSource = null;

/* LOGIKA PEMUTAR VIDEO (SINGLE PLAYER) */
function updateSingleVideo(videoData) {
    if (!videoData || !videoData.url) return;

    // A. Identifikasi Jenis dan ID Video
    let newSourceId = "";
    let isYoutube = false;
    let youtubeId = "";

    if (videoData.type === "youtube") {
        isYoutube = true;
        // Regex ambil ID Youtube
        const regExp =
            /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = videoData.url.match(regExp);
        youtubeId = match && match[2].length === 11 ? match[2] : null;
        newSourceId = "yt-" + youtubeId; // ID unik buat pengecekan
    } else {
        // Video Local
        newSourceId = "local-" + videoData.url;
    }

    if (currentVideoSource === newSourceId) {
        console.log("Video masih sama, skip reload.");
        return;
    }

    console.log("Memuat video baru:", newSourceId);
    currentVideoSource = newSourceId;
    container.innerHTML = ""; // Bersihkan container

    if (isYoutube && youtubeId) {
        // RENDER YOUTUBE (LOOPING)
        const iframe = document.createElement("iframe");
        iframe.style.width = "100%";
        iframe.style.height = "100%";
        iframe.src = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&mute=1&controls=0&loop=1&playlist=${youtubeId}`;
        iframe.frameBorder = "0";
        iframe.allow = "autoplay; encrypted-media";
        iframe.style.objectFit = "cover";
        container.appendChild(iframe);
    } else {
        // RENDER VIDEO LOKAL (LOOPING)
        const video = document.createElement("video");
        video.src = videoData.url; // Pastikan URL lengkap dari Laravel (asset/storage)
        video.style.width = "100%";
        video.style.height = "100%";
        video.style.objectFit = "cover";
        video.autoplay = true;
        video.muted = true; // Wajib mute
        video.loop = true; // WAJIB LOOP

        video.setAttribute("playsinline", "");

        container.appendChild(video);

        // Paksa play
        video.play().catch((e) => console.log("Autoplay error:", e));
    }
}

/* INTEGRASI DATA DARI LARAVEL */
async function loadDataFromLaravel() {
    try {
        const response = await fetch(API_URL);
        const data = await response.json();
        console.log("Data Update:", data);

        // A. UPDATE VIDEO
        if (data.video) {
            updateSingleVideo(data.video);
        }

        // B. UPDATE TICKER
        const ticker = document.querySelector(".ticker");
        if (ticker) ticker.innerText = data.settings.ticker;

        // C. UPDATE TANGGAL APBD
        const labelTanggal = document.querySelector(
            "h5.text-center small.fw-normal",
        );
        if (labelTanggal) labelTanggal.innerText = data.settings.apbd_date;

        // D. UPDATE AGENDA
        renderAgenda(data.agendas);

        // E. UPDATE TABEL APBD
        renderTableApbd(data.apbd.data);

        // F. UPDATE BARIS TOTAL & CHART
        if (data.apbd.total) {
            updateTotalRow(data.apbd.total);
            updateGaugeCharts(
                data.apbd.total.realisasi_pendapatan_persen,
                data.apbd.total.realisasi_belanja_persen,
                data.apbd.total.anggaran_pendapatan,
                data.apbd.total.anggaran_belanja,
            );
        }
    } catch (error) {
        console.error("Gagal konek Laravel:", error);
    }
}

// RENDER AGENDA
function renderAgenda(agendas) {
    const agendaContainer = document.getElementById("agenda-loop");
    if (!agendaContainer) return;

    let singleSetHTML = "";

    if (agendas.length === 0) {
        singleSetHTML = `<div class="p-3 text-center text-white">Belum ada agenda hari ini.</div>`;
    } else {
        agendas.forEach((item) => {
            const jamMulai = item.start_time.substring(0, 5);
            const jamSelesai = item.end_time.substring(0, 5);
            const waktuDisplay = `${jamMulai}<br>-<br>${jamSelesai}`;

            singleSetHTML += `
                <div class="agenda-card-new">
                    <div class="ac-time">WAKTU</div>
                    <div class="ac-hours text-center">${waktuDisplay}</div>
                    <div class="ac-content">
                        <h5 class="fw-bold text-warning m-0 text-uppercase lh-1 mb-1" style="font-size: 1rem;">
                            ${item.title}
                        </h5>
                        <p class="m-0 small opacity-90 lh-sm" style="font-size: 0.8rem;">
                            ${item.description || "-"}
                            <br>
                            <span class="text-warning opacity-75 fw-bold" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-location-dot me-1"></i> ${
                                    item.location
                                }
                            </span>
                        </p>
                    </div>
                </div>`;
        });
    }
    agendaContainer.innerHTML = singleSetHTML + singleSetHTML;
}

// RENDER TABEL APBD
function renderTableApbd(apbdData) {
    const tableBody = document.getElementById("table-apbd-body");
    if (!tableBody) return;

    const formatRupiah = (angka) =>
        new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(
            angka,
        );
    let singleTableHTML = "";

    if (apbdData.length === 0) {
        singleTableHTML = `<tr><td colspan="6" class="text-center p-3">Data Belum Tersedia</td></tr>`;
    } else {
        apbdData.forEach((item, idx) => {
            singleTableHTML += `
                <tr style="font-size: 0.9rem;"> <td style="width:5%; text-align:center; padding: 4px;">${
                    idx + 1
                }.</td>
                    
                    <td style="width:29%; text-align:left; padding: 4px 4px 4px 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${item.perangkat_daerah}
                    </td>
                    
                    <td style="width:24%; text-align:right; padding: 4px;">Rp ${formatRupiah(
                        item.anggaran_pendapatan,
                    )}</td>
                    
                    <td style="width:9%; text-align:center; padding: 4px;">${parseFloat(
                        item.realisasi_pendapatan,
                    ).toFixed(2)}%</td>
                    
                    <td style="width:24%; text-align:right; padding: 4px;">Rp ${formatRupiah(
                        item.anggaran_belanja,
                    )}</td>
                    
                    <td style="width:9%; text-align:center; padding: 4px;">${parseFloat(
                        item.realisasi_belanja,
                    ).toFixed(2)}%</td>
                </tr>`;
        });
    }
    tableBody.innerHTML = singleTableHTML + singleTableHTML + singleTableHTML;
}

// UPDATE BARIS TOTAL
function updateTotalRow(totalData) {
    const formatRp = (angka) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(angka);

    const elAngPend = document.getElementById("total-anggaran-pend");
    const elRealPend = document.getElementById("total-realisasi-pend");
    const elAngBel = document.getElementById("total-anggaran-bel");
    const elRealBel = document.getElementById("total-realisasi-bel");

    if (elAngPend)
        elAngPend.innerText = formatRp(totalData.anggaran_pendapatan);
    if (elRealPend)
        elRealPend.innerText =
            parseFloat(totalData.realisasi_pendapatan_persen).toFixed(2) + "%";

    if (elAngBel) elAngBel.innerText = formatRp(totalData.anggaran_belanja);
    if (elRealBel)
        elRealBel.innerText =
            parseFloat(totalData.realisasi_belanja_persen).toFixed(2) + "%";
}

// UPDATE CHART
function updateGaugeCharts(
    persenPendapatan,
    persenBelanja,
    totalPendapatan,
    totalBelanja,
) {
    const formatRpSingkat = (angka) => {
        if (angka >= 1000000000000)
            return "Rp " + (angka / 1000000000000).toFixed(1) + " T";
        if (angka >= 1000000000)
            return "Rp " + (angka / 1000000000).toFixed(1) + " M";
        return "Rp " + angka.toLocaleString();
    };

    if (window.gaugePendapatan) {
        window.gaugePendapatan.data.datasets[0].data = [
            persenPendapatan,
            100 - persenPendapatan,
        ];
        window.gaugePendapatan.update();
        const textP = document.querySelectorAll(".gauge-value")[0];
        if (textP) textP.innerText = persenPendapatan + " %";
        const labelP = document.querySelectorAll(".gauge-wrapper h6")[0];
        if (labelP)
            labelP.innerHTML = `Realisasi Pendapatan<br>${formatRpSingkat(
                totalPendapatan,
            )}`;
    }

    if (window.gaugeBelanja) {
        window.gaugeBelanja.data.datasets[0].data = [
            persenBelanja,
            100 - persenBelanja,
        ];
        window.gaugeBelanja.update();
        const textB = document.querySelectorAll(".gauge-value")[1];
        if (textB) textB.innerText = persenBelanja + " %";
        const labelB = document.querySelectorAll(".gauge-wrapper h6")[1];
        if (labelB)
            labelB.innerHTML = `Realisasi Belanja<br>${formatRpSingkat(
                totalBelanja,
            )}`;
    }
}

/* JAM & TANGGAL */
function updateClock() {
    const now = new Date();
    document.getElementById("jam").textContent =
        String(now.getHours()).padStart(2, "0") +
        ":" +
        String(now.getMinutes()).padStart(2, "0");

    const options = {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    };
    document.getElementById("tanggal").textContent = now.toLocaleDateString(
        "id-ID",
        options,
    );
}

// EKSEKUSI UTAMA
document.addEventListener("DOMContentLoaded", () => {
    setInterval(updateClock, 1000);
    updateClock();

    // Panggil data pertama kali
    loadDataFromLaravel();

    // Update data tiap 30 detik
    setInterval(loadDataFromLaravel, 30000);
});
