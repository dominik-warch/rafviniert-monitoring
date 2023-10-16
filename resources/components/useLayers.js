import { useEffect, useState} from "react";
import { createLayer } from "./LayerFactory.js";

export const useLayers = (layerConfigs, layerVisibility, setLayerVisibility) => {
    const [layers, setLayers] = useState([]);

    useEffect(() => {
        const loadLayers = async () => {
            try {
                const loadedLayers = await Promise.all(
                    Object.values(layerConfigs).map(layerConfig => createLayer(layerConfig))
                );
                setLayers(loadedLayers);
            } catch (error) {
                console.error("Failed to load layers:", error);
            }
        };

        loadLayers();
    }, [layerConfigs]);

    const handleToggleLayer = (layerId) => {
        const targetLayerIndex = layers.findIndex(layer => layer.id === layerId);
        if (targetLayerIndex === -1) return;

        const targetLayer = layers[targetLayerIndex];
        const updatedLayer = targetLayer.clone({
            visible: !targetLayer.props.visible
        });

        // Replace the target layer with the updated one
        const updatedLayers = [
            ...layers.slice(0, targetLayerIndex),
            updatedLayer,
            ...layers.slice(targetLayerIndex + 1)
        ];

        setLayers(updatedLayers);

        const updatedVisibility = {
            ...layerVisibility,
            [layerId]: !layerVisibility[layerId]
        };
        setLayerVisibility(updatedVisibility);
    };

    return { layers, layerVisibility, handleToggleLayer };
};
