import { registerBlockType } from '@wordpress/blocks';
import Edit from './blocks/company-list/edit';
import metadata from './blocks/company-list/block.json';

registerBlockType(metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null, // dynamic block (frontend rendered by render.php)
});
