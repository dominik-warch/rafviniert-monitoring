import { useEffect, useState} from "react";
import { createLayer } from "./LayerFactory.js";

export const useLayers = (layerConfigs, layerVisibility, setLayerVisibility) => {
    const [layers, setLayers] = useState([]);

    useEffect(() => {
        const loadLayers = async () => {
            const loadedLayers = await Promise.all(
                Object.values(layerConfigs).map(layerConfig => createLayer(layerConfig))
            );
            setLayers(loadedLayers);
        };

        loadLayers();
    }, [layerConfigs]);

    const handleToggleLayer = async (layerId) => {
        const updatedLayers = await Promise.all(
            layers.map(async layer => {
                if (layer.id === layerId) {
                    const originalConfig = layerConfigs[layerId];
                    const updatedLayer = await createLayer({
                        ...originalConfig,
                        initialVisible: !layer.props.visible
                    });
                    return updatedLayer;
                }
                return layer;
            })
        );
        setLayers(updatedLayers);

        const updatedVisibility = { ...layerVisibility, [layerId]: !layerVisibility[layerId] };
        setLayerVisibility(updatedVisibility);
    };

    return { layers, layerVisibility, handleToggleLayer };
};
