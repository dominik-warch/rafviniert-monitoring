import React, { useState, useMemo } from 'react';

import Sidebar from "../components/Sidebar.jsx";
import MapContainer from "../components/MapContainer.jsx";
import {useLayers} from "../components/useLayers.js";

import config from '../../config.json';

const Map = () => {
    const {initialViewState, mapStyle, layers: layerConfigs} = config.map;

    const initialLayerVisibility = useMemo(() => {
        return layerConfigs.reduce((acc, layerConfig) => {
            acc[layerConfig.id] = layerConfig.initialVisible;
            return acc;
        }, {});
    }, [layerConfigs]);

    const [layerVisibility, setLayerVisibility] = useState(initialLayerVisibility);

    const { layers, handleToggleLayer } = useLayers(layerConfigs, layerVisibility, setLayerVisibility);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    return (
        <div className="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            {isSidebarOpen && (
            <Sidebar
                layerConfigs={layerConfigs}
                onToggle={handleToggleLayer}
                layerVisibility={layerVisibility}
                setIsSidebarOpen={setIsSidebarOpen}
            />
            )}

            <main className="flex flex-col items-center justify-center flex-1">
                <button onClick={() => setIsSidebarOpen(true)} className="fixed p-2 z-40 text-white bg-black rounded-lg top-5 left-5">
                    <svg
                        className="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span className="sr-only">Open menu</span>
                </button>
                <h1 className="sr-only">Home</h1>
                <MapContainer
                    initialViewState={initialViewState}
                    layers={layers}
                    mapStyle={mapStyle}
                />
            </main>
        </div>
    );
}

export default Map;
