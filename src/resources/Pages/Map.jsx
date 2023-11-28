import React, { useCallback, useMemo, useState } from 'react';
import { ToastContainer } from 'react-toastify';

import Sidebar from "../components/Sidebar.jsx";
import DynamicLegend from "../components/DynamicLegend.jsx";
import MapContainer from "../components/MapContainer.jsx";
import {useLayers} from "../components/useLayers.js";
import useMapExport from "../components/useMapExport.js";
import MapControls from "../components/MapControls.jsx";

import config from '../../config.json';
import { toastConfig } from "../components/toastConfig.js";
import Loading from "../components/Loading.jsx";

const Map = () => {
    const {initialViewState, mapStyle, layerGroups, layers: layerConfigs} = config.map;

    const initialLayerVisibility = useMemo(() => {
        return Object.keys(layerConfigs).reduce((acc, layerId) => {
            acc[layerId] = layerConfigs[layerId].initialVisible;
            return acc;
        }, {});
    }, [layerConfigs]);

    const [layerVisibility, setLayerVisibility] = useState(initialLayerVisibility);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [isLegendOpen, setIsLegendOpen] = useState(false);

    const openSidebar = useCallback(() => setIsSidebarOpen(true), []);
    const openLegend = useCallback(() => setIsLegendOpen(true), []);

    const { layers, handleToggleLayer, isLoading } = useLayers(layerConfigs, layerVisibility, setLayerVisibility);
    const { handleExport, mapContainerRef } = useMapExport();

    return (
        <div className="flex h-screen antialiased text-gray-900 bg-gray-100 dark:bg-dark dark:text-light">
            {isLoading ? (
                <Loading />
            ) : (
                <>
                    <div className="fixed z-40 h-screen flex justify-center items-center ">
                        <MapControls
                            onSidebarOpen={openSidebar}
                            onLegendOpen={openLegend}
                            onExport={handleExport}
                        />
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

                    <ToastContainer {...toastConfig} />
                </>
            )}


        </div>
    );
}

export default Map;
