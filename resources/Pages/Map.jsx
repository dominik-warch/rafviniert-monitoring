import React, { useState, useMemo, useRef, useCallback } from 'react';
import FileSaver from "file-saver";
import { ToastContainer, toast } from 'react-toastify';

import Sidebar from "../components/Sidebar.jsx";
import DynamicLegend from "../components/DynamicLegend.jsx";
import MapContainer from "../components/MapContainer.jsx";
import {useLayers} from "../components/useLayers.js";

import config from '../../config.json';


const Map = () => {
    const mapContainerRef = useRef();

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

    const handleExport = useCallback((exportOptions) => {
        const filename = "map.png";

        if (!mapContainerRef.current) {
            console.error("Map container reference is not available");
            toast.error('Es gab einen Fehler beim Exportieren der Karte.');
            return;
        }

        requestAnimationFrame(() => {
            try {
                const mapGL = mapContainerRef.current.getMapRef().getMap();
                const maplibreCanvas = mapGL.getCanvas();

                if (!maplibreCanvas || !(maplibreCanvas instanceof HTMLCanvasElement)) {
                    console.error("Unable to get canvas from MapLibre");
                    toast.error('Es gab einen Fehler beim Exportieren der Karte.');
                    return;
                }

                maplibreCanvas.toBlob((blob) => {
                    if (!blob) {
                        console.error("Blob generation failed");
                        toast.error('Es gab einen Fehler beim Exportieren der Karte.');
                        return;
                    }
                    FileSaver.saveAs(blob, filename);
                    toast.success('Kartenexport erfolgreich!');
                });

            } catch (error) {
                console.error("Export failed", error);
                toast.error('Es gab einen Fehler beim Exportieren der Karte.');
            }
        });

    }, [mapContainerRef]);

    return (
        <div className="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">


            <div className="fixed z-40 h-screen flex justify-center items-center ">
                <div className="ml-6 flex w-16 flex-col items-center space-y-10 py-6">
                    <div className="space-y-48 rounded-md bg-white border-solid border-gray-500 border-1 shadow-lg">
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

                            <button onClick={() => handleExport(true)} className="p-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     strokeWidth="1.5" stroke="currentColor"
                                     className="h-6 w-6 cursor-pointer text-gray-500 transition-all hover:text-blue-600">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
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
                    ref={mapContainerRef}
                    initialViewState={initialViewState}
                    layers={layers}
                    mapStyle={mapStyle}
                />
            </main>

            <ToastContainer
                position="bottom-right"
                autoClose={5000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
            />
        </div>
    );
}

export default Map;
