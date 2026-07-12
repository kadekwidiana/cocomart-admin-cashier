export const TRANSACTION_STATUS_OPTIONS = [
    { value: "PENDING", label: "Menunggu Pembayaran" },
    { value: "PAID", label: "Sudah Dibayar" },
    { value: "CONFIRMED", label: "Dikonfirmasi" },
    { value: "IN_PROCESS", label: "Diproses" },
    { value: "COMPLETED", label: "Selesai" },
    { value: "CANCELED", label: "Dibatalkan" },
];

export const PICKUP_STATUS_OPTIONS = [
    { value: "PENDING", label: "Menunggu" },
    { value: "READY", label: "Siap Diambil" },
    { value: "PICKED_UP", label: "Sudah Diambil" },
    { value: "EXPIRED", label: "Kadaluarsa" },
    { value: "CANCELED", label: "Dibatalkan" },
];

export const SHIPMENT_STATUS_OPTIONS = [
    { value: "PENDING", label: "Menunggu Driver" },
    { value: "DRIVER_ASSIGNED", label: "Driver Ditugaskan" },
    { value: "PICKED_UP", label: "Barang Diambil" },
    { value: "ON_THE_WAY", label: "Dalam Pengiriman" },
    { value: "DELIVERED", label: "Terkirim" },
    { value: "CANCELED", label: "Dibatalkan" },
    { value: "FAILED", label: "Gagal Dikirim" },
];