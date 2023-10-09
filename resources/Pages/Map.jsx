import React, { useState, useMemo } from 'react';

import Sidebar from "../components/Sidebar.jsx";
import DynamicLegend from "../components/DynamicLegend.jsx";
import MapContainer from "../components/MapContainer.jsx";
import {useLayers} from "../components/useLayers.js";

import config from '../../config.json';

const Map = () => {
    const {initialViewState, mapStyle, layerGroups, layers: layerConfigs} = config.map;

    const initialLayerVisibility = useMemo(() => {
        return Object.keys(layerConfigs).reduce((acc, layerId) => {
            acc[layerId] = layerConfigs[layerId].initialVisible;
            return acc;
        }, {});
    }, [layerConfigs]);

    const [layerVisibility, setLayerVisibility] = useState(initialLayerVisibility);

    const { layers, handleToggleLayer } = useLayers(layerConfigs, layerVisibility, setLayerVisibility);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isLegendOpen, setIsLegendOpen] = useState(false);

    return (
        <div className="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">


            <div className="fixed z-40 h-screen flex justify-center items-center">
                <div className="ml-6 flex w-16 flex-col items-center space-y-10 py-6">
                    <div className="space-y-48 rounded-md bg-white">
                        <div>
                            <button onClick={() => setIsSidebarOpen(true)} className="p-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     strokeWidth="1.5" stroke="currentColor"
                                     className="h-6 w-6 cursor-pointer text-gray-500 transition-all hover:text-blue-600">
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                          d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/>
                                </svg>
                            </button>

                            <button onClick={() => setIsLegendOpen(true)} className="p-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     strokeWidth="1.5" stroke="currentColor"
                                     className="h-6 w-6 cursor-pointer text-gray-500 transition-all hover:text-blue-600">
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                          d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                </svg>
                            </button>
                        </div>

                        <a href="/dashboard" className="flex items-center justify-center pb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                                 stroke="currentColor"
                                 className="h-6 w-6 cursor-pointer text-gray-500 hover:text-blue-600">
                                <path strokeLinecap="round" strokeLinejoin="round"
                                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {isSidebarOpen && (
            <Sidebar
                layerConfigs={layerConfigs}
                layerGroups={layerGroups}
                onToggle={handleToggleLayer}
                layerVisibility={layerVisibility}
                setIsSidebarOpen={setIsSidebarOpen}
            />
            )}

            {isLegendOpen && (
                <DynamicLegend
                    layers={layers}
                    setIsLegendOpen={setIsLegendOpen}
                />
            )}

            <main className="flex flex-col items-center justify-center flex-1">
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
