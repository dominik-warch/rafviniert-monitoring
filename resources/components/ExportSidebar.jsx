import React, { useState } from "react";

const ExportSidebar = ({ onExport, setIsExportOpen }) => {
    const [pageSize, setPageSize] = useState("A4");
    const [orientation, setOrientation] = useState("landscape");
    const [format, setFormat] = useState("png");
    const [dpi, setDPI] = useState("300");

    const PAGE_SIZES = ["A2", "A3", "A4", "A5"];
    const ORIENTATIONS = ["Landscape", "Portrait"];
    const FORMATS = ["PNG", "JPG", "PDF", "SVG"];
    const DPIS = ["72", "96", "200", "300", "400"];

    const handleExport = () => {
        onExport({ pageSize, orientation, format, dpi })
        setIsExportOpen(false)
    }

    return (
        <div className="fixed inset-y-0 z-50 flex w-80 transition-transform duration-300 bg-gray-50 transform translate-x-0">
            <div className="z-10 flex flex-col flex-1">
                <div className="flex items-center justify-between flex-shrink-0 w-64 p-4">
                    <button onClick={() => setIsExportOpen(false)} className="p-1 rounded-lg focus:outline-none focus:ring">
                        <svg
                            className="w-6 h-6"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span className="sr-only">Close Export</span>
                    </button>
                </div>

                <div className="flex flex-col flex-1 p-4 mt-4">
                    <label htmlFor="pageSize" className="block text-sm font-medium leading-6 text-gray-900">
                        Seitenformat
                    </label>
                    <select
                        id="pageSize"
                        name="pageSize"
                        className="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        defaultValue="A4"
                        onChange={(e) => setPageSize(e.target.value)} value={pageSize}
                    >
                        {PAGE_SIZES.map(size => <option key={size}>{size}</option>)}
                    </select>

                    <label htmlFor="orientation" className="block text-sm font-medium leading-6 text-gray-900">
                        Ausrichtung
                    </label>
                    <select
                        id="orientation"
                        name="orientation"
                        className="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        defaultValue="Landscape"
                        onChange={(e) => setOrientation(e.target.value)} value={orientation}
                    >
                        {ORIENTATIONS.map(page_orientation => <option key={page_orientation}>{page_orientation}</option>)}
                    </select>

                    <label htmlFor="format" className="block text-sm font-medium leading-6 text-gray-900">
                        Dateiformat
                    </label>
                    <select
                        id="format"
                        name="format"
                        className="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        defaultValue="PNG"
                        onChange={(e) => setFormat(e.target.value)} value={format}
                    >
                        {FORMATS.map(export_format => <option key={export_format}>{export_format}</option>)}
                    </select>

                    <label htmlFor="dpi" className="block text-sm font-medium leading-6 text-gray-900">
                        DPI
                    </label>
                    <select
                        id="DPI"
                        name="DPI"
                        className="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        defaultValue="300"
                        onChange={(e) => setDPI(e.target.value)} value={dpi}
                    >
                        {DPIS.map(export_dpi => <option key={export_dpi}>{export_dpi}</option>)}
                    </select>

                    {/* Export Button */}
                    <button onClick={handleExport}>Export</button>
                </div>
            </div>
        </div>
    );
};

export default ExportSidebar;
