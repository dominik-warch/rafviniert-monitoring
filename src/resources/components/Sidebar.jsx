import React from "react";
import LayerControl from "./LayerControl.jsx";

const Sidebar = ({ layerConfigs, layerGroups, onToggle, layerVisibility, setIsSidebarOpen }) => {
    return (
        <div className="fixed inset-y-0 z-50 flex w-80 transition-transform duration-300 bg-gray-50 transform translate-x-0">
            <div className="z-10 flex flex-col flex-1">
                <div className="flex items-center justify-between flex-shrink-0 w-64 p-4">
                    <button onClick={() => setIsSidebarOpen(false)} className="p-1 rounded-lg focus:outline-none focus:ring">
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
                        <span className="sr-only">Close sidebar</span>
                    </button>
                </div>

                <nav className="flex flex-col flex-1 p-4 mt-4">
                    <LayerControl
                        layers={layerConfigs}
                        layerGroups={layerGroups}
                        onToggle={onToggle}
                        layerVisibility={layerVisibility}
                    />
                </nav>
            </div>
        </div>
    );
};

export default Sidebar;
