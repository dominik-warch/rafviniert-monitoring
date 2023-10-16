import React, { useState } from "react";

const PAGE_SIZES = ["A2", "A3", "A4", "A5"];
const ORIENTATIONS = ["Landscape", "Portrait"];
const FORMATS = ["PNG", "JPG", "PDF", "SVG"];
const DPIS = ["72", "96", "200", "300", "400"];

const ExportSidebar = ({ onExport, setIsExportOpen, exportOptions, setExportOptions }) => {
    const handleExport = () => {
        onExport(exportOptions)
        setIsExportOpen(false)
    }

    const handleSelectChange = (e, optionType) => {
        setExportOptions({ ...exportOptions, [optionType]: e.target.value });
    };

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
                        value={exportOptions.pageSize}
                        onChange={(e) => handleSelectChange(e, 'pageSize')}
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
                        value={exportOptions.orientation}
                        onChange={(e) => handleSelectChange(e, 'orientation')}
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
                        value={exportOptions.format}
                        onChange={(e) => handleSelectChange(e, 'format')}
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
                        value={exportOptions.dpi}
                        onChange={(e) => handleSelectChange(e, 'dpi')}
                    >
                        {DPIS.map(export_dpi => <option key={export_dpi}>{export_dpi}</option>)}
                    </select>

                    <button onClick={handleExport}>Export</button>
                </div>
            </div>
        </div>
    );
};

export default ExportSidebar;
