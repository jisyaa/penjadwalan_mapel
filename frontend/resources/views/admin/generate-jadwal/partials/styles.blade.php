<style>
    .bentrok {
        background: #ffb3b3;
        color: #900;
        font-weight: bold;
        border-radius: 4px;
        padding: 4px;
    }

    .keterangan-cell {
        text-align: center;
        vertical-align: middle;
    }

    .keterangan-cell.kuning-cerah {
        background: #fff3cd;
    }

    .keterangan-cell.kuning-cerah .keterangan-text {
        color: #856404;
    }

    .keterangan-cell.biru-cerah {
        background: #e8f4fd;
    }

    .keterangan-cell.biru-cerah .keterangan-text {
        color: #0066cc;
    }

    .keterangan-text {
        font-weight: bold;
    }

    .empty-cell {
        background: #f5f5f5;
        color: #999;
    }

    table {
        font-size: 12px;
    }

    td {
        vertical-align: middle;
        padding: 8px !important;
    }

    .tab-container {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .tab-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0;
    }

    .tab-btn {
        padding: 10px 20px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s;
        border-radius: 5px 5px 0 0;
    }

    .tab-btn:hover {
        background: #f8f9fa;
        color: #007bff;
    }

    .tab-btn.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background: #f8f9fa;
    }

    .tab-content {
        display: none;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 0 0 10px 10px;
        border: 1px solid #dee2e6;
        border-top: none;
    }

    .tab-content.active {
        display: block;
    }

    .analysis-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .analysis-table th {
        background: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
    }

    .analysis-table td {
        padding: 8px;
        text-align: center;
    }

    .status-ok {
        color: green;
        font-weight: bold;
    }

    .status-mismatch {
        color: red;
        font-weight: bold;
    }

    .guru-beban-table {
        margin-top: 20px;
    }

    .progress-bar {
        width: 100%;
        background-color: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        background-color: #28a745;
        height: 20px;
        border-radius: 10px;
        transition: width 0.3s;
        color: white;
        font-size: 10px;
        line-height: 20px;
        padding-left: 5px;
    }

    .progress-fill.warning {
        background-color: #ffc107;
        color: #333;
    }

    .progress-fill.danger {
        background-color: #dc3545;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 5px;
    }

    .bg-success {
        background-color: #28a745;
        color: white;
    }

    .bg-warning {
        background-color: #ffc107;
        color: #333;
    }

    .bg-danger {
        background-color: #dc3545;
        color: white;
    }

    .bg-info {
        background-color: #17a2b8;
        color: white;
    }

    .editable-cell {
        position: relative;
    }

    .cell-display {
        cursor: pointer;
        transition: background-color 0.2s;
        min-height: 50px;
    }

    .cell-display:hover {
        background-color: #e8f4fd;
    }

    .dropdown-select {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 8px;
        font-size: 12px;
        border: 2px solid #007bff;
        border-radius: 4px;
        background: white;
        z-index: 10;
    }

    .dropdown-select:focus {
        outline: none;
        border-color: #0056b3;
    }
</style>
